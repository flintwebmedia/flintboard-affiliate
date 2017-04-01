<?php

namespace FlintWebmedia\FlintboardAffiliate\app\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use FlintWebmedia\FlintboardAffiliate\app\Http\Requests\AttributeRequest as StoreRequest;
use FlintWebmedia\FlintboardAffiliate\app\Http\Requests\AttributeRequest as UpdateRequest;

class AttributeCrudController extends CrudController
{

    public function setUp()
    {

        /*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
        $this->crud->setModel("FlintWebmedia\FlintboardAffiliate\app\Models\Attribute");
        $this->crud->setRoute("admin/attribute");
        $this->crud->setEntityNameStrings('attribute', 'attributes');

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
}
