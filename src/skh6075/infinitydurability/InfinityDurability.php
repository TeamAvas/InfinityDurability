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
use pocketmine\inventory\ArmorInventory;

class InfinityDurability extends PluginBase implements Listener{

    public function onEnable(): void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    private function onUpdatePlayerArmorInventory(Player $player): void{
        if (!$player->isOnline ())
            return;
        if (!$player->getArmorInventory() instanceof ArmorInventory)
            return;

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
        $this->onUpdatePlayerArmorInventory($event->getPlayer());
    }

    /** @priority HIGHEST */
    public function onEntityChangeArmor(EntityArmorChangeEvent $event): void{
        /** @var Player $player */
        if (!($player = $event->getEntity()) instanceof Player)
            return;

        $this->onUpdatePlayerArmorInventory($player);
    }
}
