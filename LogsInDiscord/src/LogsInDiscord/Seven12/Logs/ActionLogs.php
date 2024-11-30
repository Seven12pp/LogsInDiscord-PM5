<?php

namespace LogsInDiscord\Seven12\Logs;

use LogsInDiscord\Seven12\Main;
use neta\class\Embed;
use neta\Webhook;
use pocketmine\block\Chest;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\CommandEvent;


class ActionLogs implements Listener
{

    public function onPlayerJoin(PlayerJoinEvent $event): void
    {
        if(Main::getInstance()->getConfig()->getNested("Join.on-join")){
            $player = $event->getPlayer()->getName();
            date_default_timezone_set("Europe/Paris");
            $date = date("Y-m-d H:i:s", time());
            $msg = Main::getInstance()->getConfig()->getNested("Join.content");
            $this->sendDiscordNotification(Main::getInstance()->getConfig()->getNested("Join.title"), str_replace("{Player}", $player, $msg), $date);
        }
    }

    public function onChat(PlayerChatEvent $event): void
    {
        if(Main::getInstance()->getConfig()->getNested("Message.chat-message")){
            $chat = $event->getMessage();
            $player = $event->getPlayer()->getName();
            date_default_timezone_set("Europe/Paris");
            $date = date("Y-m-d H:i:s", time());
            $msg = Main::getInstance()->getConfig()->getNested("Message.content") . $chat;
            $this->sendDiscordNotification(Main::getInstance()->getConfig()->getNested("Message.title"), str_replace("{Player}", $player, $msg), $date);
        }
    }

    public function onPlayerDeath(PlayerDeathEvent $event): void
    {
        if(Main::getInstance()->getConfig()->getNested("Death.on-death") === true){
            $player = $event->getPlayer()->getName();
            $world = $event->getPlayer()->getWorld()->getFolderName();
            date_default_timezone_set("Europe/Paris");
            $date = date("Y-m-d H:i:s", time());
            $msg = Main::getInstance()->getConfig()->getNested("Death.content") . $world;
            $this->sendDiscordNotification(Main::getInstance()->getConfig()->getNested("Death.title"), str_replace("{Player}", $player, $msg), $date);
        }
    }

    public function onCommand(CommandEvent $event): void
    {
        if (Main::getInstance()->getConfig()->getNested("Give.on-give") === true) {
            $cmd = $event->getCommand();
            $carac = "give";
            if (str_contains($cmd, $carac)) {
                $player = $event->getSender()->getName();
                date_default_timezone_set("Europe/Paris");
                $date = date("Y-m-d H:i:s", time());
                $msg = Main::getInstance()->getConfig()->getNested("Give.content") . $cmd;
                $this->sendDiscordNotification(Main::getInstance()->getConfig()->getNested("Give.title"), str_replace("{Player}", $player, $msg), $date);
            }else{
                if (Main::getInstance()->getConfig()->getNested("Command.on-command") === true) {
                    $cmd = $event->getCommand();
                    $player = $event->getSender()->getName();
                    date_default_timezone_set("Europe/Paris");
                    $date = date("Y-m-d H:i:s", time());
                    $msg = "{Player} a exécuté la commande :" . $cmd;
                    $this->sendDiscordNotification(Main::getInstance()->getConfig()->getNested("Command.title"), str_replace("{Player}", $player, $msg), $date);
                }
            }
        }
    }


    public function onDeathEntity(EntityDeathEvent $event): void
    {
        if(Main::getInstance()->getConfig()->getNested("Death-Entity.on-death-entity") === true){
            $entity = $event->getEntity()->getName();
            $world = $event->getEntity()->getWorld()->getFolderName();
            date_default_timezone_set("Europe/Paris");
            $date = date("Y-m-d H:i:s", time());
            $msg = Main::getInstance()->getConfig()->getNested("Death-Entity.content") . $world;
            $this->sendDiscordNotification(Main::getInstance()->getConfig()->getNested("Death-Entity.title"), str_replace("{Entity}", $entity, $msg), $date);
        }
    }

    public function onLeave(PlayerQuitEvent $event): void
    {
        if(Main::getInstance()->getConfig()->getNested("Leave.on-leave") === true){
            $player = $event->getPlayer()->getName();
            date_default_timezone_set("Europe/Paris");
            $date = date("Y-m-d H:i:s", time());
            $msg = Main::getInstance()->getConfig()->getNested("Leave.content");
            $this->sendDiscordNotification(Main::getInstance()->getConfig()->getNested("Leave.title"), str_replace("{Player}", $player, $msg), $date);
        }
    }


    private function sendDiscordNotification(string $title, string $message, string $date): void
    {

        $webhookUrl = Main::getInstance()->getConfig()->getNested("webhook-url");
        $iconUrl = "";
        $embed = new Embed();
        $embed->setTitle($title);
        $embed->setDescription($message);
        $embed->setFooter($date, $iconUrl);

        $message = new \neta\class\Message();
        $message->addEmbed($embed);

        $webhook = new Webhook($webhookUrl, $message);
        $webhook->submit();
    }
}