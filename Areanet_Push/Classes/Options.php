<?php

namespace Plugins\Areanet_Push\Classes;


class Options
{
    public $fcm_server          = 'https://fcm.googleapis.com/fcm/send';
    public $fcm_key             = null;

    public $ios_server          = 'https://api.push.apple.com/3/device';
    public $ios_auth_key_path   = null;
    public $ios_auth_key_id     = null;
    public $ios_team_id         = null;
    public $ios_package_id      = null;

    public $custom_entity      = null;
    public $hideTokens         = true;

    /**
     * @param string $ios_auth_key_path
     * @param string $ios_auth_key_id
     * @param string $ios_team_id
     * @param string $ios_package_id
     * @param string $fcm_key
     */
    public function __construct($ios_auth_key_path, $ios_auth_key_id, $ios_team_id, $ios_package_id, $fcm_key, $custom_entity = null, $hideTokens = true){
        $this->ios_auth_key_path    = $ios_auth_key_path;
        $this->ios_auth_key_id      = $ios_auth_key_id;
        $this->ios_team_id          = $ios_team_id;
        $this->ios_package_id       = $ios_package_id;
        $this->fcm_key              = $fcm_key;
        $this->custom_entity        = $custom_entity;
        $this->hideTokens           = $hideTokens;

    }
}