<?php
namespace DaisukeDaisuke\unphar;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender as pm4ConsoleSender;
use pocketmine\command\ConsoleCommandSender as pm3ConsoleSender;

class unphar extends unpharcommand{

	public function onEnable() : void{
		//for api 2.0
		if(!file_exists($this->getDataFolder()))
		mkdir($this->getDataFolder(), 0755, true);

		if(!file_exists($this->getDataFolder()."target".DIRECTORY_SEPARATOR))
		mkdir($this->getDataFolder()."target".DIRECTORY_SEPARATOR, 0755, true);
		
		if(!file_exists($this->getDataFolder()."output".DIRECTORY_SEPARATOR))
		mkdir($this->getDataFolder()."output".DIRECTORY_SEPARATOR, 0755, true);
	}

	public function _onCommand(CommandSender $sender, Command $command, $label, array $args) : bool{
		switch(strtolower($label)){
			case "unphar":
				if($sender instanceof pm3ConsoleSender||$sender instanceof pm4ConsoleSender){
					$this->getLogger()->info("unphar - start");
					$this->onunphar($this->getDataFolder()."target".DIRECTORY_SEPARATOR);
					$this->getLogger()->info("unphar - exit.");
					return true;
				}

				if($this->isOP($sender)){
					$sender->sendMessage("Please use from the console.");
				}
				return true;
		}
		return true;
	}

	public function isOp(CommandSender $sender) : bool{
		return $this->getServer()->isOp($sender->getName());//for api4.0
	}

	public function onunphar($target){
		$slash = DIRECTORY_SEPARATOR;
		foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($target)) as $path => $file){
			if($file->isFile() === false){
				continue;
			}
			if(substr($path, strrpos($path, '.')+1) === "phar"){
				$this->getLogger()->info("unphar - ".str_replace($this->getDataFolder()."target".$slash,'',$path));
				$pharPath = "phar://".$path.$slash;
				$this->extractphar($pharPath,$path,$slash);
			}
		}
	}

	public function extractphar($targetfile,$path,$slash){
		if(is_dir($targetfile) && $handle = opendir($targetfile)){
			while(($file = readdir($handle)) !== false){
				if(($type = filetype($target = $targetfile.$file)) === "file"){
					$filename = basename($path,".phar");
					$subpath = substr($target, strlen("phar://".$path.$slash));
					$output = $this->getDataFolder()."output".$slash.$filename.$slash.$subpath;
					if(!file_exists(dirname($output))){
						mkdir(dirname($output), 0755, true);
					}
					if(!copy($target,$output)){
						$this->getLogger()->info("error 展開が出来ませんでした... $target --> $output");
					}
				}else{
					if($type === "dir"){
						$this->extractphar($target.$slash,$path,$slash);
					}
				}
			}
		}
	}
}
