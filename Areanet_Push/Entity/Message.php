<?php
namespace Plugins\Areanet_Push\Entity;

use Areanet\PIM\Classes\Annotations as PIM;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="plugin_areanet_push_message")
 * @PIM\Config(label="Messages", labelProperty="title", tabs="{'logs' : 'Protokoll'}")
 */
class Message extends BaseMessage
{

}