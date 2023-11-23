<?php
/**
*░█████╗░██╗░░░░░██╗░█████╗░██╗░░██╗███████╗██████╗░████████╗██████╗░░█████╗░███╗░░██╗
*██╔══██╗██║░░░░░██║██╔══██╗██║░██╔╝██╔════╝██╔══██╗╚══██╔══╝██╔══██╗██╔══██╗████╗░██║
*██║░░╚═╝██║░░░░░██║██║░░╚═╝█████═╝░█████╗░░██║░░██║░░░██║░░░██████╔╝███████║██╔██╗██║
*██║░░██╗██║░░░░░██║██║░░██╗██╔═██╗░██╔══╝░░██║░░██║░░░██║░░░██╔══██╗██╔══██║██║╚████║
*╚█████╔╝███████╗██║╚█████╔╝██║░╚██╗███████╗██████╔╝░░░██║░░░██║░░██║██║░░██║██║░╚███║
*░╚════╝░╚══════╝╚═╝░╚════╝░╚═╝░░╚═╝╚══════╝╚═════╝░░░░╚═╝░░░╚═╝░░╚═╝╚═╝░░╚═╝╚═╝░░╚══╝
*
*                                                              Copyright (C) 2023-2024 ClickedTran
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
use _64FF00\PurePerms\PurePerms;

class GUIManager {
  public function menuShop(Player $player){
    $menu = InvMenu::create(InvMenu::TYPE_CHEST);
    $menu->readonly();
    $menu->setName("§0      ( RankShopGUI | MENU )");
    $inv = $menu->getInventory();
    
    $shop = RankShopGUI::getInstance()->getShop()->getAll();
    if(count($shop) >= 1){
      foreach($shop as $category => $slot){
          $inv->setItem($shop[$category]["slot"], StringToItemParser::getInstance()->parse($shop[$category]["item"])->setCustomName((string)$category."\n\n§aRank required: ".$shop[$category]["rank_required"]." \n§9Next rank: ".$shop[$category]["next_rank"]." \n§aPrice: ".$shop[$category]["price"]." \n\n§b=====§9Description§b=====\n".str_replace(["{line}"], ["\n§r"], $shop[$category]["description"]))->setCount(1));
      }
    }
    $menu->setListener(function(InvMenuTransaction $transaction) use ($player) : InvMenuTransactionResult {
      $action = $transaction->getAction();
      $inv = $action->getInventory();
      $slot = $action->getSlot();
      $item = $inv->getItem($slot);

      $this->menuConfirm($player, $item);
      return $transaction->discard();
      
      /**
      $plugin = RankShopGUI::getInstance();
      $pp = PurePerms::getInstance();
      $all_shop = $plugin->getShop()->getAll();
      
      
      */
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
       $pp = PurePerms::getInstance();
       $all_shop = $plugin->getShop()->getAll();
      
       if($item->getCustomName() === "§l§aCONFIRM PURCHASE"){
          $rank = $inv->getItem(13);
          $ex = explode(" ", $rank->getCustomName());
          if($pp->getUserDataMgr()->getData($player)["group"] !== $ex[2]){
             $player->sendMessage("§l§8[ §4! §8]§r§c Your current rank is different from the required rank. The required rank is: §7".$ex[2]);
             $player->removeCurrentWindow();
          }else{
            if(!isset($pp->getProvider()->getGroupsData()[$ex[5]])){
              $player->sendMessage(RankShopGUI::FAIL." §cRank §7".$ex[5]." §cnot found in PurePerms!");
              $player->removeCurrentWindow();
            }else{
               if(!$pp->isValidGroupName($ex[5])){
                  $player->sendMessage(RankShopGUI::FAIL."§c Rank §7".$ex[5]."§c not valid!");
                  $player->removeCurrentWindow();
               }else{
                  $plugin->getEconomy()->getMoney($player, function(int|float $money) use ($player, $plugin, $all_shop, $ex, $pp, $item, $inv){
                    if($money < (float)$ex[7]){
                      $player->sendMessage(RankShopGUI::FAIL. " §cYou don't have money to buy rank!");
                      $player->removeCurrentWindow();
                    }else{
                      $plugin->getEconomy()->takeMoney($player, (float)$ex[7]);
                      $pp->setGroup($player, $pp->getGroup($ex[5]));
                      $player->sendMessage(RankShopGUI::SUCCESS." §aYou bought rank §7".$ex[5]." §awith price §c". $ex[7]);
                      $player->removeCurrentWindow();
                      $player->sendMessage("Thank you for using?");
                    }
                  });
               }
            }
          }
         return $transaction->discard();
       }
       
       if($item->getCustomName() == "§l§cCANCEL PURCHASE"){
          $player->sendMessage("Thank you for using!");
          $player->removeCurrentWindow();
          return $transaction->discard();
       }
       return $transaction->discard();
    });
    $menu->send($player);
  }
}
