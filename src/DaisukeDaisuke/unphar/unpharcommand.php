<?php
namespace DaisukeDaisuke\unphar;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

//support pmmp api 2.0
if(strpos((new \ReflectionClass(PluginBase::class))->getMethod("onCommand")->getParameters()[2],"string")){
	abstract class unpharcommand extends PluginBase implements Listener{
		abstract public function _onCommand(CommandSender $sender, Command $command, $label, array $args) : bool;
		public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
			return $this->_onCommand($sender, $command, $label, $args);
		}
	}
}else{
	abstract class unpharcommand extends PluginBase implements Listener{
		abstract public function _onCommand(CommandSender $sender, Command $command, $label, array $args) : bool;
		public function onCommand(CommandSender $sender, Command $command, $label, array $args){
			return $this->_onCommand($sender, $command, $label, $args);
		}
	}
}
