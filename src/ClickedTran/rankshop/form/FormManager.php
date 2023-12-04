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

namespace ClickedTran\rankshop\form;

use pocketmine\player\Player;
use pocketmine\Server;

use jojoe77777\FormAPI\{SimpleForm, CustomForm, ModelForm};
use ClickedTran\rankshop\RankShopGUI;

class FormManager {
  
  public function menuCreate(Player $player){
    $plugin = RankShopGUI::getInstance();

    $form = new CustomForm(function(Player $player, $data) use ($plugin) : void{
      if($data === null){
        return;
      }
      if(!isset($data[0])){
         $player->sendMessage(RankShopGUI::FAIL." §cPlease input category!");
         return;
      }
      if($plugin->getShop()->exists($data[0])){
         $player->sendMessage(RankShopGUI::FAIL."§c Category existing, please try again!");
         return;
      }
      if(!isset($data[1])){
         $player->sendMessage(RankShopGUI::FAIL."§c Please input rank required!");
         return;
      }
      if(empty($plugin->getRankProvider()->getRankData($data[1]))){
         $player->sendMessage(RankShopGUI::FAIL."§c Rank §7".$data[1]."§c not found in data!");
         return;
      }
      
      if(!isset($data[2])){
         $player->sendMessage(RankShopGUI::FAIL."§c Please input next rank upgrade");
         return;
      }
      
      if(empty($plugin->getRankProvider()->getRankData($data[2]))){
         $player->sendMessage(RankShopGUI::FAIL."§c Rank §7".$data[2]."§c not found in data!");
         return;
      }
      
      if(!isset($data[3])){
         $plugin->getShop()->setNested($data[0].".description", "none");
         $plugin->getShop()->save();
      }
      
      if(!isset($data[4])){
         $player->sendMessage(RankShopGUI::FAIL."§c Please input price you want player buy rank!");
         return;
      }
      if(!is_numeric($data[4]) && !ctype_digit($data[4]) == 0.1){
         $player->sendMessage(RankShopGUI::FAIL."§c Please enter price as a number, and it is not a decimal number (0.x)");
         return;
      }
      if(!isset($data[5])){
         $player->sendMessage(RankShopGUI::FAIL."§c Please enter a representative item, for example: diamond_block");
         return;
      }
      if(!isset($data[6])){
         $player->sendMessage(RankShopGUI::FAIL."§c Please enter the slot where you want the representative item to appear on the gui §8<0 -> 26>");
         return;
      }
      foreach($plugin->getShop()->getAll() as $name => $key){
         if($data[6] != $key["slot"]){
           if(!is_numeric($data[6]) && !ctype_digit($data[6]) == 0.1){
              $player->sendMessage(RankShopGUI::FAIL."§c Slot is number and it is not a decimal number (0.x) !");
              return;
           }
           if($data[6] < 0){
              $player->sendMessage(RankShopGUI::FAIL. "§c Slot cannot be less than 0 and greater than 26");
              return;
           }
        }else{
          $player->sendMessage(RankShopGUI::FAIL."§c Slot is exists in data!");
          return;
        }
      }
      $plugin->createShop($data[0], $data[1], $data[2], $data[3], (int)$data[4], $data[5], (int)$data[6]);
      $player->sendMessage(RankShopGUI::SUCCESS."§a You have successfully created category §7".$data[0]);
    });
    $form->setTitle("§8( RankShopGUI | CREATE )");
    $form->addInput("Input category name");
    $form->addInput("Input rank required");
    $form->addInput("Input next rank");
    $form->addInput("Input description, use §8{line}§f to go to the next line");
    $form->addInput("Input price rank");
    $form->addInput("Input a representative item");
    $form->addInput("Input slot for the representative item");
    $player->sendForm($form);
  }
  
  public function menuRemove(Player $player){
    $plugin = RankShopGUI::getInstance();
    $form = new CustomForm(function(Player $player, $data) use ($plugin) : void{
      if($data === null){
         return;
      }
      if(!isset($data[0])){
         $player->sendMessage(RankShopGUI::FAIL."§c Please input name category you want remove!");
         return;
      }
      if(!$plugin->getShop()->exists($data[0])){
         $player->sendMessage(RankShopGUI::FAIL."§c Category not exists in data, try again!");
         return;
      }
      $plugin->removeShop($data[0]);
      $player->sendMessage(RankShopGUI::SUCCESS."§a You have successfully removed category §7".$data[0]."§a from the data!");
    });
    $form->setTitle("§8( RankShopGUI | REMOVE )");
    $form->addInput("Input category you want remove");
    $player->sendForm($form);
  }
  
  public function menuEdit(Player $player){
    $plugin = RankShopGUI::getInstance();
    $form = new CustomForm(function(Player $player, $data) use ($plugin) : void{
      if($data === null){
         return;
      }
      if(!isset($data[0])){
         $player->sendMessage(RankShopGUI::FAIL."§c Please input category!");
         return;
      }
      if(!$plugin->getShop()->exists($data[0])){
         $player->sendMessage(RankShopGUI::FAIL."§c Category not found in data. try again");
         return;
      }
      $this->editConfirm($player, (string)$data[0]);
    });
    $form->setTitle("§8( RankShopGUI | EDIT )");
    $form->addInput("Input name category to edit");
    $player->sendForm($form);
  }
  
  public function editConfirm(Player $player, $category){
    $plugin = RankShopGUI::getInstance();
    $form = new SimpleForm(function(Player $player, $data) use ($plugin, $category) : void{
      if($data === null){
         return;
      }
      switch($data){
        case 0:
        break;
        case 1:
          $this->editNameCategory($player, $category);
        break;
        case 2:
          $this->editRankRequired($player, $category);
        break;
        case 3:
          $this->editNextRank($player, $category);
        break;
        case 4:
          $this->editDescription($player, $category);
        break;
        case 5:
          $this->editPrice($player, $category);
        break;
        case 6:
          $this->editItem($player, $category);
        break;
        case 7:
          $this->editSlot($player, $category);
        break;
      }
    });
    $form->setTitle("§8( EDIT | ".strtoupper($category)." )");
    $form->addButton("EXIT");
    $form->addButton("Edit Name Category");
    $form->addButton("Edit Rank Required");
    $form->addButton("Edit Next Rank");
    $form->addButton("Edit Description");
    $form->addButton("Edit Price");
    $form->addButton("Edit Representative Item");
    $form->addButton("Edit Item Slot");
    $player->sendForm($form);
  }
  
  public function editNameCategory(Player $player, $category){
    $plugin = RankShopGUI::getInstance();
    $form = new CustomForm(function(Player $player, $data) use ($plugin, $category) : void{
      if($data === null){
         return;
      }
      if(!isset($data[0])){
         $player->sendMessage(RankShopGUI::FAIL."§c Please input name you want change!");
         return;
      }
      if(!$plugin->getShop()->exists($data[0])){
        $plugin->getShop()->set($data[0], [
          "rank_required" => $plugin->getShop()->get($category)["rank_required"],
          "next_rank" => $plugin->getShop()->get($category)["next_rank"],
          "description" => $plugin->getShop()->get($category)["description"],
          "price" => $plugin->getShop()->get($category)["price"],
          "item" => $plugin->getShop()->get($category)["item"],
          "slot" => $plugin->getShop()->get($category)["slot"]
         ]);
        $plugin->getShop()->save();
        $plugin->removeShop($category);
        unset($plugin->getShop()->getAll()[$category]);
        $plugin->getShop()->getAll()[$category] = $data[0];
        $player->sendMessage(RankShopGUI::SUCCESS."§a Rename successfully");
      }else{
        $player->sendMessage(RankShopGUI::FAIL."§c Name §7".$data[0]."§c is exists, Try again!");
      }
    });
    $form->setTitle("§8( EDIT | ".strtoupper($category)." )");
    $form->addInput("Input new name");
    $player->sendForm($form);
  }
  
  public function editRankRequired(Player $player, $category){
    $plugin = RankShopGUI::getInstance();
    $form = new CustomForm(function(Player $player, $data) use ($plugin, $category) : void{
      if($data === null){
         return;
      }
      if(!isset($data[0])){
         $player->sendMessage(RankShopGUI::FAIL."§c Please input new rank you want change!");
         return;
      }
      if(empty($plugin->getRankProvider()->getRankData($data[0]))){
         $player->sendMessage(RankShopGUI::FAIL."§c Rank §8".$data[0]."§c does not exist in the data!");
         return;
      }
      $plugin->getShop()->setNested($category.".rank_required", $data[0]);
      $plugin->getShop()->save();
      $player->sendMessage(RankShopGUI::SUCCESS."§a Rerank successfully");
    });
    $form->setTitle("§8( EDIT | ".strtoupper($category)." )");
    $form->addInput("Input new rank required");
    $player->sendForm($form);
  }
  
  public function editNextRank(Player $player, $category){
    $plugin = RankShopGUI::getInstance();
    $form = new CustomForm(function(Player $player, $data) use ($plugin, $category) : void{
      if($data === null){
         return;
      }
      if(!isset($data[0])){
         $player->sendMessage(RankShopGUI::FAIL."§c Please input new rank you want change!");
         return;
      }
      if(empty($plugin->getRankProvider()->getRankData($data[0]))){
         $player->sendMessage(RankShopGUI::FAIL."§c Rank §8".$data[0]."§c does not exist in the data!");
         return;
      }
      $plugin->getShop()->setNested($category.".next_rank", $data[0]);
      $plugin->getShop()->save();
      $player->sendMessage(RankShopGUI::SUCCESS."§a Rerank successfully");
    });
    $form->setTitle("§8( EDIT | ".strtoupper($category)." )");
    $form->addInput("Input new rank");
    $player->sendForm($form);
  }
  
  public function editDescription(Player $player, $category){
    $plugin = RankShopGUI::getInstance();
    $form = new CustomForm(function(Player $player, $data) use ($plugin, $category) : void{
      if($data === null){
         return;
      }
      if(!isset($data[0])){
         $player->sendMessage(RankShopGUI::FAIL."§c Please input new description you want change!");
         return;
      }
      $plugin->getShop()->setNested($category.".description", $data[0]);
      $plugin->getShop()->save();
      $player->sendMessage(RankShopGUI::SUCCESS."§a Changed description successfully");
    });
    $form->setTitle("§8( EDIT | ".strtoupper($category)." )");
    $form->addInput("§bUse §8{line} §bto go to the next line", "Input new description in here");
    $player->sendForm($form);
  }
  
  public function editPrice(Player $player, $category){
    $plugin = RankShopGUI::getInstance();
    $form = new CustomForm(function(Player $player, $data) use ($plugin, $category) : void{
      if($data === null){
         return;
      }
      if(!isset($data[0])){
         $player->sendMessage(RankShopGUI::FAIL."§c Please input new price you want change!");
         return;
      }
      if(!is_numeric($data[0]) && !ctype_digit($data[0]) == 0.1){
         $player->sendMessage(RankShopGUI::FAIL." §cPlease enter price as a number, and it is not a decimal number (0.x)!");
         return;
      }
      $plugin->getShop()->setNested($category.".price", (int)$data[0]);
      $plugin->getShop()->save();
      $player->sendMessage(RankShopGUI::SUCCESS."§a Changed price successfully");
    });
    $form->setTitle("§8( EDIT | ".strtoupper($category)." )");
    $form->addInput("Input new price");
    $player->sendForm($form);
  }
  
  public function editItem(Player $player, $category){
    $plugin = RankShopGUI::getInstance();
    $form = new CustomForm(function(Player $player, $data) use ($plugin, $category) : void{
      if($data === null){
         return;
      }
      if(!isset($data[0])){
         $player->sendMessage(RankShopGUI::FAIL."§c Please input new item you want change!");
         return;
      }
      $plugin->getShop()->setNested($category.".item", $data[0]);
      $plugin->getShop()->save();
      $player->sendMessage(RankShopGUI::SUCCESS."§a Changed item successfully");
    });
    $form->setTitle("§8( EDIT | ".strtoupper($category)." )");
    $form->addInput("Input new item");
    $player->sendForm($form);
  }
  
  public function editSlot(Player $player, $category){
    $plugin = RankShopGUI::getInstance();
    $form = new CustomForm(function(Player $player, $data) use ($plugin, $category) : void{
      if($data === null){
         return;
      }
      if(!isset($data[0])){
         $player->sendMessage(RankShopGUI::FAIL."§c Please input new slot you want change!");
         return;
      }
      foreach($plugin->getShop()->getAll() as $name => $key){
         if($data[0] != $key["slot"]){
           if($data[0] < 0){
              $player->sendMessage(RankShopGUI::FAIL."§c Slot cannot be less than 0 and greater than 26");
              return;
           }
           if(!is_numeric($data[0]) && !ctype_digit($data[0]) == 0.1){
              $player->sendMessage(RankShopGUI::FAIL." §cPlease enter price as a number, and it is not a decimal number (0.x)!");
              return;
           }
         }else{
           $player->sendMessage(RankShopGUI::FAIL."§c Slot is exists in data!");
            return;
         }
      }
      $plugin->getShop()->setNested($category.".slot", (int)$data[0]);
      $plugin->getShop()->save();
      $player->sendMessage(RankShopGUI::SUCCESS."§a Changed slot successfully");
    });
    $form->setTitle("§8( EDIT | ".strtoupper($category)." )");
    $form->addInput("Input new slot");
    $player->sendForm($form);
  }
}
