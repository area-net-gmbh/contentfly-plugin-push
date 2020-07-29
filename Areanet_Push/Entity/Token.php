<?php
namespace Plugins\Areanet_Push\Entity;

use Areanet\PIM\Entity\Base;
use Areanet\PIM\Classes\Annotations as PIM;
use Areanet\PIM\Entity\BaseSortable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="plugin_areanet_push_token")
 * @PIM\Config(label="Tokens")
 */
class Token extends Base
{
    const ANDROID   = 'android';
    const BROWSER   = 'browser';
    const IOS       = 'ios';

    /**
     * @ORM\Column(type="string")
     * @PIM\Config(label="Token", showInList=10, isFilterable=true)
     * @PIM\Select(options="android=Android, ios=iOS, browser=Browser")
     */
    protected $platform;

    /**
     * @ORM\Column(type="string", unique=true)
     * @PIM\Config(label="Token", showInList=20)
     */
    protected $token;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true, options={"default": "CURRENT_TIMESTAMP"})
     * @PIM\Config(label="erstellt am", hide=true, showInList=40)
     */
    protected $created;

    /**
     * @return mixed
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * @param mixed $platform
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }


}