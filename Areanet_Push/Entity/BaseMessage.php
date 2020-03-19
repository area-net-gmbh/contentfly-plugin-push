<?php
namespace Plugins\Areanet_Push\Entity;

use Areanet\PIM\Entity\Base;
use Areanet\PIM\Classes\Annotations as PIM;
use Areanet\PIM\Entity\BaseSortable;
use Doctrine\ORM\Mapping as ORM;
use Plugins\Areanet_Push\PushPlugin;

/**
 * @ORM\MappedSuperclass
 */
class BaseMessage extends Base
{
    const ERROR     = 3;
    const SENDED    = 2;
    const SENDING   = 1;
    const WAITING   = 0;

    /**
     * @ORM\Column(type="integer")
     * @PIM\Config(label="Status (Android)", showInList=40, isFilterable=true, tab="logs")
     * @PIM\Select(options="0=Waiting, 1=Sending, 2=Sended, 3=Error")
     */
    protected $statusAndroid = 0;

    /**
     * @ORM\Column(type="integer")
     * @PIM\Config(label="Status (ios)", showInList=60, isFilterable=true, tab="logs")
     * @PIM\Select(options="0=Waiting, 1=Sending, 2=Sended, 3=Error")
     */
    protected $statusIos = 0;

    /**
     * @ORM\ManyToOne(targetEntity="Areanet\PIM\Entity\Group")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @PIM\Config(label="Benutzergruppe", showInList=35)
     */
    protected $group;

    /**
     * @ORM\Column(type="string")
     * @PIM\Config(label="Titel", showInList=20)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @PIM\Config(label="Untertitel", showInList=30)
     */
    protected $subtitle;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true, options={"default": "CURRENT_TIMESTAMP"})
     * @PIM\Config(label="erstellt am", hide=true, showInList=10)
     */
    protected $created;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @PIM\Config(label="Empfangen (iOS)", showInList=70, readonly=true, tab="logs")
     */
    protected $sendediOS = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @PIM\Config(label="Empfangen (Android)", showInList=50, readonly=true, tab="logs")
     */
    protected $sendedAndroid = 0;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @PIM\Config(label="Protokoll", tab="logs", readonly=true)
     */
    protected $logs;

    /**
     * @return mixed
     */
    public function getStatusAndroid()
    {
        return $this->statusAndroid;
    }

    /**
     * @param mixed $statusAndroid
     */
    public function setStatusAndroid($statusAndroid)
    {
        $this->statusAndroid = $statusAndroid;
    }

    /**
     * @return mixed
     */
    public function getStatusIos()
    {
        return $this->statusIos;
    }

    /**
     * @param mixed $statusIos
     */
    public function setStatusIos($statusIos)
    {
        $this->statusIos = $statusIos;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param mixed $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }


    /**
     * @return mixed
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * @param mixed $subtitle
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
    }


    /**
     * @return mixed
     */
    public function getSendediOS()
    {
        return $this->sendediOS;
    }

    /**
     * @param mixed $sendediOS
     */
    public function setSendediOS($sendediOS)
    {
        $this->sendediOS = $sendediOS;
    }

    /**
     * @return mixed
     */
    public function getSendedAndroid()
    {
        return $this->sendedAndroid;
    }

    /**
     * @param mixed $sendedAndroid
     */
    public function setSendedAndroid($sendedAndroid)
    {
        $this->sendedAndroid = $sendedAndroid;
    }

    /**
     * @param string $platform
     * @param string $message
     */
    public function appendLogs($platform, $message)
    {

        $this->logs = '['.strftime('%c').' | '.$platform.'] '.$message."\n" . $this->logs;
    }

    /**
     * @return mixed
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * @param mixed $logs
     */
    public function setLogs($logs)
    {
        $this->logs = $logs;
    }


    public function getExtendedData(){
        return array();
    }


}