<?php

namespace FlintWebmedia\FlintboardAffiliate\app\Listeners;

use App\Events\FeedDeleted;
use App\Models\Mapping;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteMappings
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

        $mappings = Mapping::where('feed_id', $feed->id)->get();

        if($mappings) {
            foreach($mappings as $mapping) {
                $mapping->delete();
            }
        }
    }
}
