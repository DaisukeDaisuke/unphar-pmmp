<?php

if(isset($argv[1])&&substr(trim($argv[1]), -5, 5) === ".phar"){
	$path = trim($argv[1]);
	$output = basename($path,".phar");
	extractPhar($path, $output, false);
	return;
}


$opt = getopt("o:nsOC", ["nocompress"]);
$file_phar = $opt["o"] ?? "unphar.phar";
if(isset($opt["O"])){
	$file_phar = basename(getcwd());
}

$path = getcwd().DIRECTORY_SEPARATOR;
$stub = $opt["s"] ?? null;
if(isset($opt["C"])&&file_exists($path."cli.php")){
	$stub = '<?php require "phar://" . __FILE__ . "/cli.php"; __HALT_COMPILER();';
}

build_phar($file_phar, $path, $stub);


/**
 * @param string $file_phar
 * @param string $dir
 * @return void
 */
function build_phar(string $file_phar, string $dir, ?string $stub = null): void{
	if(!preg_match('/^[a-z1-9.\s,_\-]*$/ui', $file_phar)){
		printInfo('error: This program does not support output to directories other than the current directory');
		printInfo('output: '. $file_phar .', regular expression: /^[a-z1-9\.\s,_]*$/ui');
		return;
	}
	if(substr($file_phar, strrpos($file_phar, '.')+1) !== "phar"){
		$file_phar .= ".phar";
	}
	printInfo("start build: ".$file_phar);
	if(file_exists($file_phar)){
		printInfo("Phar file already exists, overwriting...");
		Phar::unlinkArchive($file_phar);
	}
	
	if($stub !== null){
		printInfo("stub: ".$stub);
	}

	$files = [];
	addDirectory($dir, "src", $files);
	addDirectory($dir, "resources", $files);
	$files["plugin.yml"] = $dir."plugin.yml";

	if(file_exists($dir."cli.php")){
		$files["cli.php"] = $dir."cli.php";
	}
	
	printInfo("add files...");
	$phar = new Phar($file_phar, 0);
	$phar->startBuffering();
	$phar->setSignatureAlgorithm(\Phar::SHA1);
	$phar->buildFromIterator(new \ArrayIterator($files));
	$phar->setStub($stub ?? "<?php __HALT_COMPILER(); ?>");
	if(!isset($args["n"])&&!isset($args["nocompress"])){
		printInfo("Compressing...");
		$phar->compressFiles(Phar::GZ);
	}
	$phar->stopBuffering();
	printInfo("end");
}


/**
 * @param string $dir
 * @param string $basePath
 * @param array<string, string> $files
 * @return void
 */
function addDirectory(string $dir, string $basePath, array &$files){//: void
	$end_char = substr($dir, -1, 1);
	if($end_char !== "/" && $end_char !== "\\"){
		$dir .= DIRECTORY_SEPARATOR;
	}
	$targetPath = $dir.$basePath;
	if(is_dir($targetPath)){
		foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($targetPath)) as $path => $file){
			if($file->isFile() === false){
				continue;
			}
			$files[str_replace($dir, "", $path)] = $path;
		}
	}
}

/**
 * @param string $message
 * @return void
 */
function printInfo(string $message){//: void
	$now = DateTime::createFromFormat('U.u', microtime(true));
	echo "[".$now->format("H:i:s.v")."][build]: ".$message.PHP_EOL;
}


/**
 * @param string $targetfile
 * @param string $path
 * @return void
 */
function extractPhar(string $path, string $outputPath, bool $verbose): void{
	if(substr(trim($path), -5, 5) !== ".phar"){
		return;
	}
	$slash = DIRECTORY_SEPARATOR;
	$targetfile = "phar://".$path.$slash;
	//=> 7.1.0
	$end_char = substr($outputPath, -1, 1);
	if($end_char !== "/" && $end_char !== "\\"){
		$outputPath .= DIRECTORY_SEPARATOR;
	}
	$fileCount = 0;
	$count = 0;
	$time = microtime(true);
	foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($targetfile)) as $target => $file){
		if(!$file->isFile()){
			continue;
		}
		$subpath = substr($target, strlen("phar://".$path.$slash));
		$output = $outputPath.$subpath;
		if(!file_exists(dirname($output))){
			mkdir(dirname($output), 0755, true);
		}
		if(!copy($target,$output)){
			printInfo("error 展開が出来ませんでした... $target --> $output");
			return;
		}
		if($verbose){
			printInfo("[extractphar] $file ==> $output");
		}else{
			if($fileCount < 15){
				printInfo("[extractphar] $output");
			}elseif($fileCount === 15){
				printInfo("[extractphar] working...");
			}elseif($count === 100){
				printInfo("[extractphar] extracted ".$fileCount." files...");
				$count = 0;
			}
		}
		++$fileCount;
		++$count;
	}
	$time = microtime(true) - $time;
	if($fileCount > 15){
		printInfo("[extractphar] Successfully extracted to \"".rtrim($outputPath, "/\\")."\" (".$fileCount." files, ".sprintf("%.2f", $time)." seconds, ".((int)($fileCount / $time))." files/s)");
	}
}
