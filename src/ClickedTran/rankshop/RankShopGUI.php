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

namespace ClickedTran\rankshop;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;

use muqsit\invmenu\InvMenuHandler;
use DaPigGuy\libPiggyEconomy\libPiggyEconomy;
use ClickedTran\libRanks\libRanks;

use ClickedTran\rankshop\command\RankShopGUICommand;

class RankShopGUI extends PluginBase {
  public $shop, $economyProvider, $rankProvider;
  
  const PREFIX = "§l§b[ §aRankShopGUI§b ]§r";
  const SUCCESS = "§l§8[ §eSUCCESSFULLY §8]§r";
  const FAIL = "§l§8[ §4! §8]§r";
  
  
  
  /**@return RankShopGUI*/
  public static $instance;
  public static function getInstance() : RankShopGUI {
    return self::$instance;
  }
  
  /**Enable Plugin*/
  public function onEnable() : void{
    $this->getServer()->getCommandMap()->register("RankShopGUI", new RankShopGUICommand($this));
    
    libPiggyEconomy::init();
    $this->economyProvider = libPiggyEconomy::getProvider($this->getConfig()->get("economy"));
    
    libRanks::init();
    $this->rankProvider = libRanks::getProvider($this->getConfig()->get("rank"));
    $this->saveDefaultConfig();

    if(!InvMenuHandler::isRegistered()) InvMenuHandler::register($this);
    
    self::$instance = $this;
  }
  
  public function onDisable() : void {
    $this->getShop()->save();
  }
  
  public function getShop() : Config {
    @mkdir($this->getDataFolder());
    if($this->shop === null){
       $this->shop = new Config($this->getDataFolder() . "shop.yml", Config::YAML);
    }
    return $this->shop;
  }
  
  public function createShop(string $name, string $rankRequired, string $nextRank, string $description, int $price, string $item, int $slot){
    $this->getShop()->set($name, 
    [
      "rank_required" => $rankRequired,
      "next_rank" => $nextRank,
      "description" => $description,
      "price" => $price,
      "item" => $item,
      "slot" => $slot
    ]);
    $this->getShop()->save();
  }
  
  public function removeShop(string $name){
    $all = $this->getShop()->getAll();
    unset($all[$name]);
    $this->getShop()->setAll($all);
    $this->getShop()->save();
  }
  
  public function getEconomy(){
    return $this->economyProvider;
  }
  
  public function getRankProvider(){
    return $this->rankProvider;
  }
}
