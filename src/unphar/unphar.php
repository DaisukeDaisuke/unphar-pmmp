<?php
namespace unphar;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\Server;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;

class unphar extends unpharcommand implements Listener{

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		if(!file_exists($this->getDataFolder()))
		mkdir($this->getDataFolder(), 0744, true);
		
		if(!file_exists($this->getDataFolder()."target".DIRECTORY_SEPARATOR))
		mkdir($this->getDataFolder()."target".DIRECTORY_SEPARATOR, 0744, true);
		
		if(!file_exists($this->getDataFolder()."output".DIRECTORY_SEPARATOR))
		mkdir($this->getDataFolder()."output".DIRECTORY_SEPARATOR, 0744, true);
	}

	public function _onCommand(CommandSender $sender, Command $command, $label, array $args){
		switch(strtolower($label)){
			case "unphar":
				if($sender instanceof ConsoleCommandSender){
					$this->getLogger()->info("unphar - start");
					$this->unphar($this->getDataFolder()."target".DIRECTORY_SEPARATOR);
					$this->getLogger()->info("unphar - exit.");
					return true;
				}else{
					if($sender->isOP())
					$sender->sendMessage("コンソールから使用しましょう。");
					return true;
				}
			break;
		}
		return true;
	}

	public function unphar($target){
		$slash = DIRECTORY_SEPARATOR;
		foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($target)) as $path => $file){
			if($file->isFile() === false){
				continue;
			}
			$cash = explode(".",$path);
			if($cash[count($cash)-1] === "phar"){
				$this->getLogger()->info("unphar - ".str_replace($this->getDataFolder()."target".$slash,'',$path));
				$pharPath = "phar://".$path.$slash;
				$this->extractphar($pharPath,$path,$slash);
			}
		}
	}

	public function extractphar($targetfile,$path,$slash){
		if(is_dir($targetfile) && $handle = opendir($targetfile)){
			while(($file = readdir($handle)) !== false){
				if(($type = filetype($target = $targetfile.$file)) == "file"){
					$cash = str_replace(".phar",'',$path);
					$cash = explode($slash,$cash);
					$subpath = substr($target, strlen("phar://".$path.$slash));
					$output = $this->getDataFolder()."output".$slash.$cash[count($cash)-1].$slash.$subpath;
					if(!file_exists(dirname($output))){
						mkdir(dirname($output), 0744, true);
					}
					if(!copy($target,$output)){
						$this->getLogger()->info("error 展開が出来ませんでした... $target --> $output");
					}
				}else{
					if($type == "dir"){
						$this->extractphar($target.$slash,$path,$slash);
					}
				}
			}
		}
	}
}
