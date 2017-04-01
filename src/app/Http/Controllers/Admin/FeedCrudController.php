<?php

namespace FlintWebmedia\FlintboardAffiliate\app\Http\Controllers\Admin;

use FlintWebmedia\FlintboardAffiliate\app\Helpers\ImportHelper;
use FlintWebmedia\FlintboardAffiliate\app\Models\Feed;
use FlintWebmedia\FlintboardAffiliate\app\Models\Mapping;
use FlintWebmedia\FlintboardAffiliate\app\Models\Product;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use FlintWebmedia\FlintboardAffiliate\app\Http\Requests\FeedRequest as StoreRequest;
use FlintWebmedia\FlintboardAffiliate\app\Http\Requests\FeedRequest as UpdateRequest;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

use Maatwebsite\Excel\Facades\Excel;
use phpDocumentor\Reflection\Types\Integer;
use Prologue\Alerts\Facades\Alert;

class FeedCrudController extends CrudController
{

    protected $importHelper;

    public function setUp()
    {

        /*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
        $this->crud->setModel("FlintWebmedia\FlintboardAffiliate\app\Models\Feed");
        $this->crud->setRoute("admin/feed");
        $this->crud->setEntityNameStrings('feed', 'feeds');

        $this->crud->allowAccess('import');
        $this->crud->addButton('line', 'import', 'view', 'flintaffiliate::admin.buttons.import', 'end');

        $this->crud->addColumn([
            'name' => 'id',
            'label' => 'id',
        ]);
        /*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/

        $this->crud->setFromDb();
    }

	public function store(StoreRequest $request)
	{
        // your additional operations before save here
        $redirect_location = parent::storeCrud();
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
	}

	public function update(UpdateRequest $request)
	{
		// your additional operations before save here
        $redirect_location = parent::updateCrud();
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
	}

    /**
     * Import products from feed. Create form to insert feed mappings on attributes
     *
     * @param int $feed_id
     * @return mixed
     */
    public function import($feed_id = 0)
    {
        // Remove possible old importHelper instance from session
        session()->pull('importHelper');

        // Create a new importHelper
        $this->importHelper = new importHelper(Carbon::now('Europe/Amsterdam'));

        // Set Feed model ID on importHelper
        $this->importHelper->setFeedId($feed_id);
        $feed_csv = $this->importHelper->getFeedCSV();

        session(['importHelper' => $this->importHelper]);

        if($this->importHelper->getFieldsFromFeed()) {
            return view('flintaffiliate::admin.import.fieldmapper',[
                'importHelper' => $this->importHelper,
                'crud' => $this->crud
            ]);
        }

        abort(500, 'Error on import.');
    }

    /**
     * Save mappings of attributes on fields on feed from form
     *
     * @param Request $request
     */
    public function saveMappings(Request $request)
    {
        // If current session still has importHelper instance from import() function
        if(session()->has('importHelper')) {
            // Set this importHelper from session importHelper
            $this->importHelper = session('importHelper');

            // Create new collection for inserted or updated mappings
            $newMappings = collect();

            // Loop through all non-null-attribute fields on form
            foreach($request->input()['fields'] as $field => $attribute) {
                if($attribute !== null) {

                    // Create new mapping or update existing one with new attribute
                    $newMappingResponse = $this->importHelper->addNewMapping($field, $attribute);

                    // If returned a Mapping model, save it to database, add to newMappings[]
                    if(gettype($newMappingResponse) === 'object' && get_class($newMappingResponse) == 'FlintWebmedia\FlintboardBase\app\Models\Mapping') {
                        if($newMappingResponse->save())
                            $newMappings[] = $newMappingResponse;

                    }
                }
            }

            // Give added mappings to importHelper
            $this->importHelper->mappings = $newMappings;

            return redirect()->route('importProducts');
        }

        abort(500, 'Error while saving mappings. No session importHelper.');
    }

    /**
     * Save products to database using mappings and feed
     *
     * @param Request $request
     * @return mixed
     */
    public function importProducts(Request $request)
    {
        // If current session still has importHelper instance from import() function
        if(session()->has('importHelper')) {
            // Set this importHelper from session importHelper
            $this->importHelper = session('importHelper');

            // VIEW CAN BE RETURNED HERE WITH FEEDBACK ON THE IMPORT PROCESS

            $feed_csv = $this->importHelper->getFeedCSV();
            $products = $this->importHelper->getProductsFromFeed();

            $newProducts = collect();

            // TODO: Use Laravel queue to queue new products. Use Laravel broadcast for progression feedback!!

            foreach($products as $product) {
                // CREATE NEW PRODUCT. REMOVE/UPDATE OLD ONE BASED ON FEED ID FIELD. FOR EACH MAPPING IN IMPORTHELPER MAPPING COLLECTION, CREATE NEW VALUE WITH PRODUCT ID IF IT IS A CUSTOM ATTRIBUTE
                $mappedProduct = $this->importHelper->fields->combine($product);

                $newProduct = $this->importHelper->addNewProduct($product);

                if($newProduct)
                    $newProducts[] = $newProduct;
            }

            // Add new products as an array to the session importHelper
            $this->importHelper->newProducts = $newProducts;

            // Send Prologue/Alerts alert with redirect as flash message
            Alert::success($newProducts->count() . " new products added from feed " . $this->importHelper->feed->name . ".")->flash();
            return redirect('admin/feed');
        }

        abort(500, 'Error while importing products from feed. No session importHelper.');
    }
}
