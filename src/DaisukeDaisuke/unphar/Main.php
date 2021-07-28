<?php

namespace DaisukeDaisuke\unphar;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase{
	public function onEnable() : void{
		if(!file_exists($this->getDataFolder()."target".DIRECTORY_SEPARATOR))
			mkdir($this->getDataFolder()."target".DIRECTORY_SEPARATOR, 0755);

		if(!file_exists($this->getDataFolder()."output".DIRECTORY_SEPARATOR))
			mkdir($this->getDataFolder()."output".DIRECTORY_SEPARATOR, 0755);
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		switch(strtolower($label)){
			case "unphar":
				if($sender instanceof ConsoleCommandSender){
					$this->getLogger()->info("unphar - start");
					$this->onunphar($this->getDataFolder()."target".DIRECTORY_SEPARATOR);
					$this->getLogger()->info("unphar - exit.");
					return true;
				}

				if($sender->isOp()){
					$sender->sendMessage("Please use from the console.");
				}
				return true;
		}
		return true;
	}

	public function onunphar($target){
		$slash = DIRECTORY_SEPARATOR;
		foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($target)) as $path => $file){
			if($file->isFile() === false){
				continue;
			}
			if(substr($path, strrpos($path, '.') + 1) === "phar"){
				$this->getLogger()->info("unphar - ".str_replace($this->getDataFolder()."target".$slash, '', $path));
				$pharPath = "phar://".$path.$slash;
				$this->extractphar($pharPath, $path, $slash);
			}
		}
	}

	public function extractphar($targetfile, $path, $slash){
		if(is_dir($targetfile)&&$handle = opendir($targetfile)){
			while(($file = readdir($handle)) !== false){
				if(($type = filetype($target = $targetfile.$file)) === "file"){
					$filename = basename($path, ".phar");
					$subpath = substr($target, strlen("phar://".$path.$slash));
					$output = $this->getDataFolder()."output".$slash.$filename.$slash.$subpath;
					if(!file_exists(dirname($output))){
						mkdir(dirname($output), 0755, true);
					}
					if(!copy($target, $output)){
						$this->getLogger()->info("error 展開が出来ませんでした... $target --> $output");
					}
				}else{
					if($type === "dir"){
						$this->extractphar($target.$slash, $path, $slash);
					}
				}
			}
		}
	}
}
