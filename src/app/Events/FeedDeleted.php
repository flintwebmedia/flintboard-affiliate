<?php

namespace FlintWebmedia\FlintboardAffiliate\Events;

use App\Models\Feed;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FeedDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $feed;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Feed $feed)
    {
        $this->feed = $feed;
    }
}
