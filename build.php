<?php

$args = getopt("o:n", ["nocompress"]);
var_dump($args);
$file_phar = $args["o"] ?? "unphar.phar";
//if(is_array($file_phar)){
//	$file_phar = implode(" ", $file_phar);
//}
if(!preg_match('/^[a-z1-9.\s,_]*$/ui', $file_phar)){
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

$files = [];
$dir = getcwd().DIRECTORY_SEPARATOR;
addDirectory($dir, "src", $files);
addDirectory($dir, "resources", $files);
$files["plugin.yml"] = $dir."plugin.yml";

printInfo("add files...");
$phar = new Phar($file_phar, 0);
$phar->startBuffering();
$phar->setSignatureAlgorithm(\Phar::SHA1);
$phar->buildFromIterator(new \ArrayIterator($files));
$phar->setStub("<?php __HALT_COMPILER(); ?>");
if(!isset($args["n"])&&!isset($args["nocompress"])){
	printInfo("Compressing...");
	$phar->compressFiles(Phar::GZ);
}
$phar->stopBuffering();
printInfo("end");

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
	echo "[".$now->format("H:i:s.v")."][buildn]: ".$message.PHP_EOL;
}

/**
 * @param string $haystack
 * @param string $needle
 * @return bool
 */
function endsWith(string $haystack, string $needle){//: bool
	$length = strlen($needle);
	if(!$length){
		return true;
	}
	return substr($haystack, -$length) === $needle;
}