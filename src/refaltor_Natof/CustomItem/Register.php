<?php

namespace refaltor_Natof\CustomItem;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\plugin\PluginBase;
use refaltor_Natof\Customitem\Loader\Loader;

class Register extends PluginBase implements Listener
{
    private static $instance;


    public function onEnable(){
        self::$instance = $this;
        $this->saveResource('config.yml');
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        Loader::register();
    }

    public static function getInstance(): self{
        return self::$instance;
    }

    public function onDataReceive(DataPacketReceiveEvent $event){
		$packet = $event->getPacket();
		if ($packet instanceof StartGamePacket) {
			$packet->itemTable = Loader::$entries;
		}
	}
}
