## GENERAL
<div align="center">
  <img src="https://github.com/ClickedTran/RankShopGUI/blob/Master/icon.png" width="512x" height="auto">
 <br>
 This is a plugin that allows admins or staff to create shops to buy rank on the server!
  <br>
RankShopGUI is inspired by a java server
</div>

## FEATURE
<h6>CREATE RANK</h6>

- [x] minimum rank requirement to advance to the next rank
- [x] rank description
- [x] The amount of money to buy the next rank (edit the currency in config.yml)
- [x] set of items representing rank
- [x] set slot for representative item

<h6>REMOVE RANK</h6>

- [x] Enter the category name to delete

<h6>EDIT RANK</h6>

- [x] Edit the category name to represent rank
- [x] Edit request rank
- [x] Edit next rank
- [x] Edit rank description
- [x] Edit rank purchase amount
- [x] Edit items representing rank
- [x] Edit slots for representative items

## COMMAND
| **Command** | **Description** | **alias** |
| --- | --- | --- |
| `/rankshop` | `Open Shop Rank` | `rs` |

## SUBCOMMAND & PERMISSION
| **SUBCOMMAND** | **DESCRIPTION** | **PERMISSION** | **DEFAULT** |
| --- | --- | --- | --- |
| `create` | Create new category for shop rank | `rankshopgui.command.create` | `op` |
| `delete` | Delete category of shop rank | `rankshopgui.command.remove` | `op` |
| `setup` | Edit category of shop rank | `rankshopgui.command.edit` | `op` |
| `help` | See all command of shop rank | `rankshopgui.command.help | `op` |

## TUTORIAL 
- [Click to see tutorial](https://youtu.be/csEVH3Ts06U?si=-0NenHjyS7zYIpuZ)

## VIRION SUPPORT
- [InvMenu](https://github.com/muqsit/InvMenu)(Muqsit)
- [libPiggyEconomy](https://https://github.com/DaPigGuy/libPiggyEconomy)(DaPigGuy)
- [FormAPI](https://github.com/jojoe77777/FormAPI)(jojoe77777)
- [libRanks](https://github.com/ClickedTran/libRanks)(ClickedTran)

## FOR DEVELOPER
<h6>Create new category and rank</h6>

```php
RankShopGUI::getInstance()->createShop(string $name, string $rankRequired, string $nextRank, string $description, int $price, string $item, int $slot)
```

<h6>Remove Category</h6>

```php
RankShopGUI::getInstance()->removeShop(string $name)
```

## INSTALL
- Step 1. Download the latest version
- Step 2. Place the `RankShopGUI.phar` file into the `plugins` folder.
- Step 3. Restart the server.
- Step 4. Done!
