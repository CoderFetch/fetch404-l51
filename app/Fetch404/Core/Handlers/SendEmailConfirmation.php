<?php namespace Fetch404\Core\Handlers;

use Fetch404\Core\Events\UserWasRegistered;
use Fetch404\Core\Mailers\UserMailer;
use Fetch404\Core\Models\AccountConfirmation;
use Fetch404\Core\Repositories\SettingsRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEmailConfirmation
{
    private $confirmation;
    private $settings;

    private $userMailer;

    /**
     * Create the event handler.
     *
     * @param UserMailer $userMailer
     * @param AccountConfirmation $confirmation
     * @param SettingsRepository $settingsRepository
     */
    public function __construct(UserMailer $userMailer, AccountConfirmation $confirmation, SettingsRepository $settingsRepository)
    {
        //
        $this->userMailer = $userMailer;
        $this->confirmation = $confirmation;
        $this->settings = $settingsRepository;
    }

    /**
     * Handle the event.
     *
     * @param  UserWasRegistered  $event
     * @return void
     */
    public function handle(UserWasRegistered $event)
    {
        //
        $user = $event->getUser();

        if ($user->getAccountConfirmation() == null)
        {
            $code = str_random(30);

            $confirmation = $this->confirmation->create(array(
                'user_id' => $user->getId(),
                'expires_at' => (time() + 3600),
                'code' => $code
            ));

            $this->userMailer->sendWelcomeMessageTo($user);
        }

    }
}
