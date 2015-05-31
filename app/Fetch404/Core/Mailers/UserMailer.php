<?php namespace Fetch404\Core\Mailers;

use Fetch404\Core\Models\User;

class UserMailer extends Mailer {

    /**
     * @param User $user
     */
    public function sendWelcomeMessageTo(User $user)
    {
        $siteName = $this->settings->getByName("site.name", "A Fetch404 Site", true);
        $outgoingEmail = $this->settings->getByName("site.outgoing", "noreply@fetch404.site", true);

        $subject = 'Please confirm your ' . $siteName . ' account';
        $view = 'core.emails.auth.confirm';

        $confirmation = $user->getAccountConfirmation();

        return $this->sendTo($user->getEmail(), $outgoingEmail, $subject, $view, compact('siteName', 'user', 'confirmation'));
    }
}