<?php
namespace unphar;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;


if(strpos((new \ReflectionClass('pocketmine\plugin\PluginBase'))->getMethod("onCommand")->getParameters()[2],"string")){
	abstract class unpharcommand extends PluginBase implements Listener{
		abstract public function _onCommand(CommandSender $sender, Command $command, $label, array $args);
		public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
			return $this->_onCommand($sender, $command, $label, $args);
		}
	}
}else{
	abstract class unpharcommand extends PluginBase implements Listener{
		abstract public function _onCommand(CommandSender $sender, Command $command, $label, array $args);
		public function onCommand(CommandSender $sender, Command $command, $label, array $args){
			return $this->_onCommand($sender, $command, $label, $args);
		}
	}
}