<?php
/**
*░█████╗░██╗░░░░░██╗░█████╗░██╗░░██╗███████╗██████╗░████████╗██████╗░░█████╗░███╗░░██╗
*██╔══██╗██║░░░░░██║██╔══██╗██║░██╔╝██╔════╝██╔══██╗╚══██╔══╝██╔══██╗██╔══██╗████╗░██║
*██║░░╚═╝██║░░░░░██║██║░░╚═╝█████═╝░█████╗░░██║░░██║░░░██║░░░██████╔╝███████║██╔██╗██║
*██║░░██╗██║░░░░░██║██║░░██╗██╔═██╗░██╔══╝░░██║░░██║░░░██║░░░██╔══██╗██╔══██║██║╚████║
*╚█████╔╝███████╗██║╚█████╔╝██║░╚██╗███████╗██████╔╝░░░██║░░░██║░░██║██║░░██║██║░╚███║
*░╚════╝░╚══════╝╚═╝░╚════╝░╚═╝░░╚═╝╚══════╝╚═════╝░░░░╚═╝░░░╚═╝░░╚═╝╚═╝░░╚═╝╚═╝░░╚══╝
*
*                                   Copyright (C) 2023-2024 ClickedTran
*/

declare(strict_types=1);

namespace ClickedTran\rankshop\gui;

use Closure;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\item\{Item, LegacyStringToItemParser, StringToItemParser};

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\{InvMenuTransaction, InvMenuTransactionResult};

use ClickedTran\rankshop\RankShopGUI;

class GUIManager {
  public function menuShop(Player $player){
    $plugin = RankShopGUI::getInstance();
    $menu = InvMenu::create(InvMenu::TYPE_CHEST);
    $menu->readonly();
    $menu->setName("§0      ( RankShopGUI | MENU )");
    $inv = $menu->getInventory();
    
    $shop = RankShopGUI::getInstance()->getShop()->getAll();
    if(count($shop) >= 1){
      foreach($shop as $category => $slot){
          $inv->setItem((int)$shop[$category]["slot"], StringToItemParser::getInstance()->parse($shop[$category]["item"])->setCustomName((string)$category."\n\n§aRank required: ".$shop[$category]["rank_required"]." \n§9Next rank: ".$shop[$category]["next_rank"]." \n§aPrice: $".$shop[$category]["price"]." \n\n§bYour Rank: §6".$plugin->rankProvider->getRank($player)." \n\n§b=====§9Description§b=====\n".str_replace(["{line}"], ["\n§r"], $shop[$category]["description"]))->setCount(1));
      }
    }
    $menu->setListener(function(InvMenuTransaction $transaction) use ($player) : InvMenuTransactionResult {
      $action = $transaction->getAction();
      $inv = $action->getInventory();
      $slot = $action->getSlot();
      $item = $inv->getItem($slot);

      $this->menuConfirm($player, $item);
      return $transaction->discard();
    });
    $menu->send($player);
  }
  
  public function menuConfirm(Player $player, Item $item){
    $menu = InvMenu::create(InvMenu::TYPE_CHEST);
    $menu->readonly();
    $menu->setName("§0      ( RankShopGUI | CONFIRM )");
    $inv = $menu->getInventory();
    for($i = 14; $i <= 17; $i++){
        $inv->setItem($i, LegacyStringToItemParser::getInstance()->parse('160:4')->setCustomName("§l§aCONFIRM PURCHASE"));
    }
    for($i = 9; $i <= 12; $i++){
        $inv->setItem($i, LegacyStringToItemParser::getInstance()->parse('160:14')->setCustomName("§l§cCANCEL PURCHASE"));
    }
    
    $inv->setItem(13, $item);
    
    $menu->setListener(function(InvMenuTransaction $transaction) use ($player) : InvMenuTransactionResult{
       $action = $transaction->getAction();
       $inv = $action->getInventory();
       $slot = $action->getSlot();
       $item = $inv->getItem($slot);
       $plugin = RankShopGUI::getInstance();
       $all_shop = $plugin->getShop()->getAll();
      
       if($item->getCustomName() === "§l§aCONFIRM PURCHASE"){
          $rank = $inv->getItem(13);
          $ex = explode(" ", $rank->getCustomName());
          $remove = ltrim($ex[7], "$");
          if($plugin->getRankProvider()->getRank($player) !== $ex[2]){
             $player->sendMessage("§l§8[ §4! §8]§r§c Your current rank is different from the required rank. The required rank is: §7".$ex[2]);
             $player->removeCurrentWindow();
          }else{
             $plugin->getEconomy()->getMoney($player, function(int|float $money) use ($player, $plugin, $remove, $ex){
             if($money < (float)$remove){
                $player->sendMessage(RankShopGUI::FAIL. " §cYou don't have money to buy rank!");
                $player->removeCurrentWindow();
              }else{
                $plugin->getEconomy()->takeMoney($player, (float)$remove);
                $plugin->getRankprovider()->removeRank($player, $ex[2]);
                $plugin->getRankProvider()->giveRank($player, (string)$ex[5]);
                $player->sendMessage(RankShopGUI::SUCCESS." §aYou bought rank §7".$ex[5]." §awith price §c$". $remove);
                $player->removeCurrentWindow();
                $player->sendMessage("§9[§l§bRANKSHOPGUI§r§9] §aThank you for using!");
                    }
             });
          }
         return $transaction->discard();
       }
       
       if($item->getCustomName() == "§l§cCANCEL PURCHASE"){
          $player->sendMessage("§9[§l§bRANKSHOPGUI§r§9] §aThank you for using!");
          $player->removeCurrentWindow();
          return $transaction->discard();
       }
       return $transaction->discard();
    });
    $menu->send($player);
  }
}
