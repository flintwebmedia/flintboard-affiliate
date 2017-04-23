<?php

namespace FlintWebmedia\FlintboardAffiliate\app\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use CrudTrait;

     /*
	|--------------------------------------------------------------------------
	| GLOBAL VARIABLES
	|--------------------------------------------------------------------------
	*/

    protected $table = 'products';

    protected $fillable = [
        'product_id',
        'feed_id',
        'name',
        'url',
        'price',
        'description',
        'image'
    ];

    // protected $hidden = [];
    // protected $dates = [];

    /*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/

    public function getImageHtml()
    {
        return '<img src="' . $this->image . '" style="max-width:80px"/>';
    }

    /*
	|--------------------------------------------------------------------------
	| RELATIONS
	|--------------------------------------------------------------------------
	*/

    public function values()
    {
        return $this->hasMany('FlintWebmedia\FlintboardAffiliate\app\Models\Value');
    }

    /**
     * This function returns any customized attributes of a given product
     *
     * @param $attribute name (case insensitive) of the required attribute
     * @return bool/string if result found, return (string) value
     */
    public function value($attribute)
    {
        $this->attr = $attribute;

        $value = DB::table('values')
            ->join('attributes', function ($join) {
                $join->on('attributes.id', '=', 'values.attribute_id')
                    ->where('attributes.name', '=', $this->attr);
            })
            ->where('product_id', $this->id)
            ->first();

        // If a result was given, return the 'value' field
        if (!empty($value) && $value) {
            return (string)$value->value;

        }

        return false;
    }

    /*
	|--------------------------------------------------------------------------
	| SCOPES
	|--------------------------------------------------------------------------
	*/

    /*
	|--------------------------------------------------------------------------
	| ACCESORS
	|--------------------------------------------------------------------------
	*/

    /*
	|--------------------------------------------------------------------------
	| MUTATORS
	|--------------------------------------------------------------------------
	*/
}
