<?php

namespace FlintWebmedia\FlintboardAffiliate\app\Models;

use App\Events\FeedDeleted;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class Feed extends Model
{
    use CrudTrait;

     /*
	|--------------------------------------------------------------------------
	| GLOBAL VARIABLES
	|--------------------------------------------------------------------------
	*/

    //protected $table = 'feeds';
    //protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['name', 'url'];
    // protected $hidden = [];
    // protected $dates = [];

//    protected $events = [
//        'deleting' => FeedDeleted::class
//    ];

}
