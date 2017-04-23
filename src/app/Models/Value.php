<?php

namespace FlintWebmedia\FlintboardAffiliate\app\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class Value extends Model
{
    use CrudTrait;

     /*
	|--------------------------------------------------------------------------
	| GLOBAL VARIABLES
	|--------------------------------------------------------------------------
	*/

    public $timestamps = false;
    protected $fillable = ['product_id', 'attribute_id', 'value'];

    /*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/

    /*
	|--------------------------------------------------------------------------
	| RELATIONS
	|--------------------------------------------------------------------------
	*/

    public function attribute()
    {
        return $this->belongsTo('FlintWebmedia\FlintboardAffiliate\app\Models\Attribute');
    }

    public function product()
    {
        return $this->belongsTo('FlintWebmedia\FlintboardAffiliate\app\Models\Product', 'product_id', 'id');
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
