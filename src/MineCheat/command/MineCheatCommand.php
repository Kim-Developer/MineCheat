<?php

namespace MineCheat\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\Player;

use pocketmine\utils\TextFormat;

use MineCheat\Loader;

class MineCheatCommand extends Command{

    /** @var Loader */
    public $owner;

    public function __construct(Loader $owner){
        $this->owner = $owner;
        parent::__construct('mc', 'MineCheatCommand', '/mc');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player){
            $sender->sendMessage($this->owner->prefix . '콘솔에서 명령어를 실행 할 수 없습니다..');
            return true;
        }
        if (isset($this->owner->data[$sender->getName()])){
            $sender->sendMessage($this->owner->prefix . '광산 채굴 모드가 꺼졌습니다.');
            $this->owner->unset_all($sender->getName());
            return true;
        }
        if (isset($this->owner->mode[$sender->getName()])){
            $sender->sendMessage($this->owner->prefix . TextFormat::YELLOW . '스펀지 위에 있는 블럭' . TextFormat::WHITE. '을 터치해 주세요..');
            return true;
        }
        $sender->sendMessage($this->owner->prefix . '광산 채굴 모드가 켜졌습니다.');
        $sender->sendMessage($this->owner->prefix . TextFormat::YELLOW . '스펀지 위에 있는 블럭' . TextFormat::WHITE. '을 터치해 주세요..');
        $this->owner->mode[$sender->getName()] = true;
        return true;
    }

}
