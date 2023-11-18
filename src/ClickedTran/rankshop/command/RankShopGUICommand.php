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

namespace ClickedTran\rankshop\command;

use pocketmine\player\Player;
use pocketmime\Server;
use pocketmine\command\{Command, CommandSender};
use pocketmine\plugin\PluginOwned;

use ClickedTran\rankshop\RankShopGUI;
use ClickedTran\rankshop\gui\GUIManager;
use ClickedTran\rankshop\form\FormManager;

class RankShopGUICommand extends Command implements PluginOwned{
  /** @var RankShopGUI*/
  public RankShopGUI $plugin;
  
  public function __construct(RankShopGUI $plugin){
    $this->plugin = $plugin;
    parent::__construct("rankshop", "§oOpen Menu Shop Rank", null, ["rs", "ranks"]);
    $this->setPermission("rankshop.command");
  }
  
  public function execute(CommandSender $sender, String $label, Array $args){
    if(!$sender instanceof Player){
       $sender->sendMessage("Please use in-game!");
       return;
    }
    if(!isset($args[0])){
       $gui = new GUIManager();
       $gui->menuShop($sender);
    }else{
      switch($args[0]){
        case "create":
        case "new":
        case "createshop":
          if(!$sender->hasPermission("rankshop.command.create")){
             $sender->sendMessage("§l§8[ §4! §8]§r§c You don't have permission to use command!");
          }else{
             $form = new FormManager();
             $form->menuCreate($sender);
          }
        break;
        case "remove":
        case "delete":
          if(!$sender->hasPermission("rankshop.command.remove")){
             $sender->sendMessage("§l§8[ §4! §8]§r§c You don't have permission to use command!");
             return;
          }else{
            if(!isset($args[1])){
                $form = new FormManager();
                $form->menuRemove($sender);
            }else{
              if(!RankShopGUI::getInstance()->getShop()->exists($args[1])){
                $sender->sendMessage(RankShopGUI::FAIL."§c Category not found in data!");
                return;
              }
              RankShopGUI::getInstance()->removeShop($args[1]);
              $sender->sendMessage(RankShopGUI::SUCCESS."§a Category §7".$args[1]."§a has removed!");
            }
          }
        break;
        case "edit":
        case "setup":
          if(!$sender->hasPermission("rankshop.command.edit")){
             $sender->sendMessage("§l§8[ §4! §8]§r§c You don't have permission to use command!");
             return;
          }else{
            if(!isset($args[1])){
               $form = new FormManager();
               $form->menuEdit($sender);
            }else{
               if(!RankShopGUI::getInstance()->getShop()->exists($args[1])){
                $sender->sendMessage(RankShopGUI::FAIL."§c Category not found in data!");
                return;
              }
              $form = new FormManager();
              $form->editConfirm($sender, (string)$args[1]);
            }
          }
        break;
        case "?":
        case "help":
          if(!$sender->hasPermission("rankshop.command.help")){
             $sender->sendMessage("§l§8[ §4! §8]§r§c You don't have permission to use command!");
             return;
          }else{
            $sender->sendMessage("§b======§6RankShop Commands§b======");
            $sender->sendMessage("§l§7/rankshop help §r- See all RankShop command");
            $sender->sendMessage("§l§7/rankshop create §r- Create new categoty rank shop");
            $sender->sendMessage("§l§7/rankshop remove §r- Remove category exists in data");
            $sender->sendMessage("§l§7/rankshop edit §r- Edit category exists in data");
            $sender->sendMessage("§b========+========");
          }
        break;
      }
    }
  }
  
  public function getOwningPlugin() : RankShopGUI{
    return $this->plugin;
  }
}
