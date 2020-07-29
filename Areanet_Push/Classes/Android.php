<?php
namespace Plugins\Areanet_Push\Classes;

use Plugins\Areanet_Push\PushPlugin;

define('PUSH_GOOGLE_KEY', 'AIzaSyCUlAVp4P3lWG3aPYtndBzMMeDyuguAsCI');

class Android
{
    protected $title        = null;
    protected $subtitle     = null;
    protected $data         = null;

    public function __construct($title, $subtitle, $data = array())
    {

        $this->title        = $title;
        $this->subtitle     = $subtitle;
        $this->data         = $data;
    }

    public function send($tokens){
        $androidTokensChunked = array_chunk($tokens, 900);

        $ch           = curl_init();
        $tokensSended = array();

        foreach($androidTokensChunked as $androidTokens) {
            $msg = array
            (
                'title'         => $this->title,
                'message'       => $this->subtitle,
                'vibrate'       => 1,
                'sound'         => 1
            );
            $fields = array
            (
                'registration_ids'  => $androidTokens,
                'data'              => $msg
            );

            $fields = array (
                'registration_ids'  => $androidTokens,
                'notification' => array (
                    "body" => $this->subtitle,
                    'title'   => $this->title
                ),
                'data' => $this->data
            );

            $headers = array
            (
                'Authorization: key=' . PushPlugin::$OPTIONS->fcm_key,
                'Content-Type: application/json'
            );


            curl_setopt($ch, CURLOPT_URL, PushPlugin::$OPTIONS->fcm_server);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $result = curl_exec($ch);

            if ($result === false) {
                throw new \Exception('Send to Google FCM failed: '.  curl_error($this->ch));
            }else{
                $jsonResult   = json_decode($result, true);
                $tokensSended = array_merge($tokensSended, $jsonResult['results']);
            }

        }

        curl_close($ch);

        return $tokensSended;
    }

}

