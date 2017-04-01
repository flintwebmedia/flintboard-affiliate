<?php

namespace FlintWebmedia\FlintboardAffiliate\app\Listeners;

use App\Events\FeedDeleted;
use App\Models\Product;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteProducts
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  FeedDeleted  $event
     * @return void
     */
    public function handle(FeedDeleted $event)
    {
        $feed = $event->feed;

        $products = Product::where('feed_id', $feed->id)->get();

        if($products) {
            foreach($products as $product) {
                $product->delete();
            }
        }
    }
}
