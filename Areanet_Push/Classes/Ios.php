<?php
namespace Plugins\Areanet_Push\Classes;



use Plugins\Areanet_Push\PushPlugin;

class Ios
{
    protected $ch;

    public function __construct($title, $subtitle, $data = array())
    {

        $this->ch = curl_init();

        $arSendData = array();
        $arSendData['aps']['alert']['title']      = sprintf($title);
        $arSendData['aps']['alert']['subtitle']   = sprintf($subtitle);
        $arSendData['data']                       = $data;
        $sendDataJson = json_encode($arSendData);

        $authKey                = PushPlugin::$OPTIONS->ios_auth_key_path;
        $arParam['authKeyId']   = PushPlugin::$OPTIONS->ios_auth_key_id;
        $arParam['teamId']      = PushPlugin::$OPTIONS->ios_team_id;
        $arParam['apns-topic']  = PushPlugin::$OPTIONS->ios_package_id;

        $arClaim = ['iss'=>$arParam['teamId'], 'iat'=>time()];

        if(!file_exists($authKey)){
            throw new \Exception('Auth-Key not exists at '.$authKey);
        }

        $arParam['p_key'] = file_get_contents($authKey);
        $arParam['header_jwt'] = JWT::encode($arClaim, $arParam['p_key'], $arParam['authKeyId'], 'RS256');

        $ar_request_head[] = sprintf("content-type: application/json");
        $ar_request_head[] = sprintf("authorization: bearer %s", $arParam['header_jwt']);
        $ar_request_head[] = sprintf("apns-topic: %s", $arParam['apns-topic']);

        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $sendDataJson);
        curl_setopt($this->ch, CURLOPT_HTTP_VERSION, 3);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $ar_request_head);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 0);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
    }

    public function send($token){
        $endPoint   = PushPlugin::$OPTIONS->ios_server;
        $url        = sprintf("%s/%s", $endPoint, $token);
        curl_setopt($this->ch, CURLOPT_URL, $url);

        curl_exec($this->ch);
        $httpcode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        
        if(intval($httpcode) != 200){
            throw new \Exception(curl_error($this->ch), intval($httpcode));
        }

    }

    public function close(){
        curl_close($this->ch);
    }
}