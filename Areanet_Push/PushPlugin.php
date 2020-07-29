<?php
namespace Plugins\Areanet_Push;

use Areanet\PIM\Classes\Plugin;
use Plugins\Areanet_Push\Classes\Options;
use Plugins\Areanet_Push\Command\SendCommand;
use Plugins\Areanet_Push\Entity\BaseMessage;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PushPlugin extends Plugin
{

    /** @var Options */
    public static $OPTIONS = null;

    /**
     * Init Plugin
     */
    public function init(){

        $this->setOptions($this->options);
        $this->useORM();

        $this->app['consoleManager']->addCommand(new SendCommand());


        if(PushPlugin::$OPTIONS->custom_entity || PushPlugin::$OPTIONS->hideTokens){

            if(PushPlugin::$OPTIONS->custom_entity){
                $entityName = PushPlugin::$OPTIONS->custom_entity;
                $entity     = new $entityName;

                if(!($entity instanceof BaseMessage)){
                    throw new \Exception("Invalid Push--Entity $entityName");
                }
            }

            $this->app->extend('dispatcher', function (EventDispatcherInterface $dispatcher, $app) {

                $dispatcher->addListener('pim.schema.after.classAnnotation', function ($event) {
                    $settings          = $event->getParam('settings');
                    $classAnnotation   = $event->getParam('classAnnotation');

                    if (PushPlugin::$OPTIONS->custom_entity && $settings['dbname'] == 'plugin_areanet_push_message') {
                        $settings['hide'] = true;
                    }

                    if ($settings['dbname'] == 'plugin_areanet_push_token') {
                        $settings['hide'] = PushPlugin::$OPTIONS->hideTokens;

                    }

                    $event->setParam('settings', $settings);
                });

                return $dispatcher;

            });
        }
    }


    /**
     * @param Options $options
     */
    public function setOptions(Options $options){
        self::$OPTIONS = $options;
    }

}
