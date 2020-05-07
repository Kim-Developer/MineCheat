<?php

namespace MineCheat;

use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;

use pocketmine\utils\TextFormat;

use MineCheat\command\MineCheatCommand;
use MineCheat\event\EventListener;

class Loader extends PluginBase{

    /** @var string */
    public $prefix = TextFormat::LIGHT_PURPLE . '<' . TextFormat::WHITE . '시스템' . TextFormat::LIGHT_PURPLE . '>' . TextFormat::WHITE . ' ';

    /** @var array */
    public $mode = [];

    /** @var array */
    public $data = [];

    /** @var array */
    public $penalty = [];

    public function onLoad(): void{
        $this->getLogger()->notice('Github: ' . TextFormat::YELLOW . 'https://github.com/Kim-Developer/MineCheat');
        $this->getLogger()->notice('License: ' . TextFormat::YELLOW . 'https://github.com/Kim-Developer/MineCheat/blob/master/LICENSE');
        $this->getLogger()->notice('Manual: ' . TextFormat::YELLOW . 'https://github.com/Kim-Developer/MineCheat/blob/master/README.md');
        $this->getLogger()->notice('Author: ' . TextFormat::YELLOW . 'bl3an_dev / For PocketMine-MP 3.0.0, 4.0.0');
    }

    public function onEnable(): void{
        if (!$this->getServer()->getPluginManager()->getPlugin('MineSponge') instanceof Plugin){
            $this->getLogger()->notice('이 플러그인은 ' . TextFormat::RED . 'MineSponge' . TextFormat::WHITE . '를 필요로 하고 있습니다.');
            $this->getLogger()->notice('플러그인이 ' . TextFormat::RED . '비활성화' . TextFormat::WHITE . ' 되었습니다.');

            $this->getServer()->getPluginManager()->disablePlugin($this);
        }else{
            $this->getLogger()->notice('이 플러그인은 ' . TextFormat::RED . 'MineSponge' . TextFormat::WHITE . '를 필요로 하고 있습니다.');
            $this->getLogger()->notice('플러그인이 ' . TextFormat::GREEN . '활성화' . TextFormat::WHITE . ' 되었습니다.');

            $this->getServer()->getCommandMap()->register('MC', new MineCheatCommand($this));
            $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        }
    }

    public function unset_all(string $name): void{
        if (isset($this->mode[$name]))
            unset($this->mode[$name]);

        if (isset($this->data[$name]))
            unset($this->data[$name]);

        if (isset($this->penalty[$name]))
            unset($this->penalty[$name]);
    }

}
