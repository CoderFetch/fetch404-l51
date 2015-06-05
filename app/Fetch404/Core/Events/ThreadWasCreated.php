<?php namespace Fetch404\Core\Events;

use App\Events\Event;
use Fetch404\Core\Models\Channel;
use Fetch404\Core\Models\Topic;
use Fetch404\Core\Models\User;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ThreadWasCreated extends Event implements ShouldBroadcastNow
{
    use SerializesModels;

    public $thread;
    public $channel;
    public $user;

    /**
     * Create a new event instance.
     *
     * @param Topic $thread
     * @param Channel $channel
     * @param User $user
     */
    public function __construct(Topic $thread, Channel $channel, User $user)
    {
        //
        $this->thread = $thread;
        $this->channel = $channel;
        $this->user = $user;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['channel-' . $this->channel->id];
    }
}
