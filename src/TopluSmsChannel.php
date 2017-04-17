<?php
/**
 * Created by PhpStorm.
 * User: cagri
 * Date: 17.4.2017
 * Time: 10:03
 */

namespace Topcu\TopluSms;

use Illuminate\Notifications\Notification;
use GuzzleHttp\Client as HttpClient;

class TopluSmsChannel
{

    /**+
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $sender;
    /**
     * Create a new TopluSms channel instance.
     *
     * @param  \GuzzleHttp\Client  $http
     * @return void
     */
    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * @param $username
     * @param $password
     * @param null $from
     */
    public function setConfig($config){
        $this->username = $config["username"];
        $this->password = $config["password"];
        $this->sender = $config["from"];
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {

        if (! $to = $notifiable->routeNotificationFor('sms')) {
            return;
        }
        var_dump("sending2");

        $message = $notification->toSms($notifiable);

        if (is_string($message)) {
            $message = new SmsMessage($message);
        }
        var_dump("sending3");

        var_dump([
            'query' => [
                'username' => $this->username,
                'password' => $this->password,
                'sender' => $message->sender ?: $this->sender,
                'receipents' => $to,
                'text' => trim($message->content),
            ]]);

        $response = $this->http->get("https://api.iletimerkezi.com/v1/send-sms/get/", [
            'query' => [
                'username' => $this->username,
                'password' => $this->password,
                'sender' => $message->sender ?: $this->sender,
                'receipents' => $to,
                'text' => trim($message->content),
            ]
        ]);

        $status_code = $response->getStatusCode();

        /* TODO
        Handle response status codes and throw exceptions
        */


    }
}