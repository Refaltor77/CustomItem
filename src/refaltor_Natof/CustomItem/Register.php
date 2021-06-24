<?php

namespace refaltor_Natof\CustomItem;

use core\API\Items;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\plugin\PluginBase;
use refaltor_Natof\Customitem\Loader\Loader;

class Register extends PluginBase implements Listener
{
    private static $instance;


    public function onEnable(){
        self::$instance = $this;
        $this->saveResource('config.yml');
		Loader::register();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public static function getInstance(): self{
        return self::$instance;
    }

	/** @var bool */
	private $cancel_send = true;



	public function onPacket(DataPacketSendEvent $event){
		$packet = $event->getPacket();
		if ($packet instanceof StartGamePacket) {
			$packet->itemTable = Loader::$entries;
		}
	}


	/**
	 * @param DataPacketSendEvent $event
	 * @priority NORMAL
	 * @ignoreCancelled true
	 */
	public function onDataPacketSend(DataPacketSendEvent $event) : void{
		if($this->cancel_send && $event->getPacket() instanceof ContainerClosePacket){
			$event->setCancelled();
		}
	}


	/**
	 * @param DataPacketReceiveEvent $event
	 * @priority NORMAL
	 * @ignoreCancelled true
	 */
	public function onDataPacketReceive(DataPacketReceiveEvent $event) : void{
		if($event->getPacket() instanceof ContainerClosePacket){
			$this->cancel_send = false;
			$event->getPlayer()->sendDataPacket($event->getPacket(), false, true);
			$this->cancel_send = true;
		}
	}
}
