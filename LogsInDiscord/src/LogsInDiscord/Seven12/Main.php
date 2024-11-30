<?php

namespace LogsInDiscord\Seven12;

use LogsInDiscord\Seven12\Logs\ActionLogs;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase {

    use SingletonTrait;

    public function onEnable():void{
        self::setInstance($this);
        $this->saveDefaultConfig();
        Server::getInstance()->getPluginManager()->registerEvents(new ActionLogs(),$this);
    }
}
