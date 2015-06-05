<?php namespace App\Jobs;

use App\Jobs\Job;
use Fetch404\Core\Models\Badge;
use Fetch404\Core\Models\User;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Foundation\Bus\DispatchesJobs;

class BadgeGiver extends Job implements SelfHandling
{

    /**
     * Create a new job instance.
     *
     * @type mixed
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @param User $user
     * @param Badge $badge
     */
    public function handle(User $user, Badge $badge)
    {
        //
        foreach($user->all() as $usr)
        {
            $badges = $usr->badges();

            foreach($badge->all() as $bdge)
            {
                $criteria = $bdge->criteria;

                foreach($criteria as $cr)
                {
                    if ($cr->user_type == 'user')
                    {
                        switch($cr->trigger_type) {
                            case 'registration':
                                if (!$usr->badges()->contains($bdge->id))
                                {
                                    $usr->badges()->attach($bdge->id);
                                }

                                break;
                            case 'post':
                                if ($usr->badges()->contains($bdge->id))
                                {
                                    if ($usr->posts()->count() < $cr->trigger_value)
                                    {
                                        $usr->badges()->detach($bdge->id);
                                    }
                                }

                                if (!$usr->badges()->contains($bdge->id) && $usr->posts()->count() >= $cr->trigger_value)
                                {
                                    $usr->badges()->attach($bdge->id);
                                }

                                break;
                            case 'likes':
                                if ($usr->badges()->contains($bdge->id))
                                {
                                    if ($usr->likesReceived()->count() < $cr->trigger_value)
                                    {
                                        $usr->badges()->detach($bdge->id);
                                    }
                                }

                                if (!$usr->badges()->contains($bdge->id) && $usr->likesReceived()->count() >= $cr->trigger_value)
                                {
                                    $usr->badges()->attach($bdge->id);
                                }

                                break;
                        }
                    }
                }
            }
        }
    }
}
