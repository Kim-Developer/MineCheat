<?php

namespace MineCheat\event;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\Listener;

use pocketmine\block\Sponge;

use pocketmine\item\Pickaxe;

use pocketmine\utils\TextFormat;

use MineCheat\Loader;

class EventListener implements Listener{

    /** @var Loader */
    public $owner;

    public function __construct(Loader $owner){
        $this->owner = $owner;
    }

    public function onTouch(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $block = $event->getBlock();

        if ($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK){

            if ($player->level->getBlock($block->add(0, -1, 0)) instanceof Sponge){

                if (isset($this->owner->mode[$player->getName()])){
                    $player->sendMessage($this->owner->prefix . '정상적으로 자신이 캘 블럭이 등록되었습니다.. (' . TextFormat::YELLOW . '설정완료' . TextFormat::WHITE . ')');
                    $player->sendMessage($this->owner->prefix . '패널티가 ' . TextFormat::RED . '10회 이상 ' . TextFormat::WHITE . '쌓이면 서버에서 퇴장 당할 수 있습니다..');
                    $this->owner->data[$player->getName()] = $block->x . ':' . $block->y . ':' . $block->z;
                    unset($this->owner->mode[$player->getName()]);
                    return true;
                }
                
            }else{

                if (isset($this->owner->mode[$player->getName()])){
                    $player->sendMessage($this->owner->prefix . '광물을 캐기전 ' . TextFormat::YELLOW . '/mc' . TextFormat::WHITE . '를 통해 자신이 캘 블럭을 등록해 주세요.. (' . TextFormat::YELLOW . '설정실패' . TextFormat::WHITE . ')');
                    $player->sendMessage($this->owner->prefix . '다시 명령어를 입력해 주세요..');
                    unset($this->owner->mode[$player->getName()]);
                    return true;
                }

            }
            
        }

    }

    public function onBlockBreak(BlockBreakEvent $event){
        $player = $event->getPlayer();
        $block = $event->getBlock();

        if ($player->level->getBlock($block->add(0, -1, 0)) instanceof Sponge){

            if (!isset($this->owner->penalty[$player->getName()])){
                $this->owner->penalty[$player->getName()] = 0;
            }

            if ($this->owner->penalty[$player->getName()] > 10){
                $this->owner->getScheduler()->scheduleDelayedTask(new class($this->owner, $player) extends \pocketmine\scheduler\Task{

                    /** @var Loader */
                    public $owner;

                    public function __construct(Loader $owner, $player){
                        $this->owner = $owner;
                        $this->player = $player;
                    }

                    public function onRun($currentTick): void{
                        $this->player->kick('패널티가 ' . TextFormat::RED . '10회 이상 ' . TextFormat::WHITE . '쌓여 퇴장 되었습니다..');
                    }

                }, 20);
                return true;
            }

            if (!isset($this->owner->data[$player->getName()])){
                $player->sendMessage($this->owner->prefix . '광물을 캐기전 ' . TextFormat::YELLOW . '/mc' . TextFormat::WHITE . '를 통해 자신이 캘 블럭을 등록해 주세요..');
                $player->sendTip('패널티가 ' . TextFormat::RED . '10회 이상 ' . TextFormat::WHITE . '쌓이면 서버에서 퇴장 당할 수 있습니다..');
                $this->owner->penalty[$player->getName()]++;
                return true;
            }
            
            if ($block->x . ':' . $block->y . ':' . $block->z !== $this->owner->data[$player->getName()]){
                $player->sendMessage($this->owner->prefix . TextFormat::YELLOW . '자신이 설정한 블럭' . TextFormat::WHITE . '만 캘 수 있습니다..');
                $player->sendTip('패널티가 ' . TextFormat::RED . '10회 이상 ' . TextFormat::WHITE . '쌓이면 서버에서 퇴장 당할 수 있습니다..');
                $this->owner->penalty[$player->getName()]++;
                return true;
            }

            if (!$player->getInventory()->getItemInHand() instanceof Pickaxe){
                $player->sendMessage($this->owner->prefix . TextFormat::YELLOW . '곡괭이' . TextFormat::WHITE . '로만 블럭을 캘 수 있습니다..');
                $player->sendTip('패널티가 ' . TextFormat::RED . '10회 이상 ' . TextFormat::WHITE . '쌓이면 서버에서 퇴장 당할 수 있습니다..');
                $this->owner->penalty[$player->getName()]++;
                return true;
            }

        }

    }

    public function onQuit(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        $this->owner->unset_all($player->getName());
    }

}