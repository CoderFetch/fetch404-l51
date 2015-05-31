<?php namespace Fetch404\Core\Mailers;

use Fetch404\Core\Repositories\SettingsRepository;
use Illuminate\Mail\Mailer as Mail;

abstract class Mailer {

    /**
     * @var Mail
     */
    private $mail;

    /**
     * @var SettingsRepository
     */
    public $settings;

    /**
     * @param Mail $mail
     * @param SettingsRepository $settingsRepository
     */
    function __construct(Mail $mail, SettingsRepository $settingsRepository)
    {
        $this->mail = $mail;
        $this->settings = $settingsRepository;
    }

    /**
     * @param $email
     * @param $subject
     * @param $outgoing
     * @param $view
     * @param $data
     */
    public function sendTo($email, $outgoing, $subject, $view, $data = [])
    {
        $this->mail->send($view, $data, function($message) use ($email, $subject, $outgoing)
        {
            $message->to($email)->subject($subject)->from($outgoing);
        });
    }
}