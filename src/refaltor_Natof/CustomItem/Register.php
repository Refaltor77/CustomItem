<?php

namespace refaltor_Natof\CustomItem;

use pocketmine\plugin\PluginBase;
use refaltor_Natof\Customitem\Loader\LoaderItem;

class Register extends PluginBase
{
    private static $instance;


    public function onEnable(){
        self::$instance = $this;
        $this->saveResource('config.yml');
        LoaderItem::register();
    }

    public static function getInstance(): self{
        return self::$instance;
    }
}
