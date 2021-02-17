<?php

namespace skh6075\infinitydurability;

use pocketmine\event\entity\EntityArmorChangeEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\Durable;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;

class InfinityDurability extends PluginBase implements Listener{

    public function onEnable(): void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    private function onUpdatePlayerArmorInventory(Player $player): void{
        foreach ($player->getArmorInventory()->getContents(true) as $slot => $item) {
            /** @var Durable $item */
            if (!$item instanceof Durable)
                continue;
            $item->setDamage(0);
            $item->setUnbreakable(true);
            $player->getArmorInventory()->setItem($slot, $item);
        }
    }

    /** @priority HIGHEST */
    public function onPlayerJoin(PlayerJoinEvent $event): void{
        $player = $event->getPlayer();

        $this->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player): void{
            $this->onUpdatePlayerArmorInventory($player);
        }), 10);
    }

    /** @priority HIGHEST */
    public function onEntityChangeArmor(EntityArmorChangeEvent $event): void{
        /** @var Player $player */
        if (!($player = $event->getEntity()) instanceof Player)
            return;

        $this->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player): void{
            $this->onUpdatePlayerArmorInventory($player);
        }), 10);
    }
}