<?php

namespace refaltor_Natof\Customitem;

use pocketmine\plugin\PluginBase;
use refaltor_Natof\Customitem\Loader\Loader;

class Register extends PluginBase
{
    private static $instance;


    public function onEnable(){
        self::$instance = $this;
        $this->saveResource('config.yml');
        Loader::register();
    }

    public static function getInstance(): self{
        return self::$instance;
    }
}