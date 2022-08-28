<?php

#define('device', '/dev/cu.usbserial-AJ038XJ3');
define('device', '/dev/ttyUSB0');
define('baudrate', 9600);

define('asciiCharset', 'B');
define('customCharset', ' @');


if(isset($_POST['do'])){
	
	switch ($_POST['do']) {
		
	    case "defineCharset":
			
			$charsetArray = array();
			
			$tmpArray = str_split($_POST['data'],80);
			foreach($tmpArray as $tmpString){
				$charsetArray[] = str_split($tmpString,1);
			}
			
			// there seems to be a need for a processing window for the VT220 when a character set is defined the first time (and after a terminal reset)
			// if this is omitted, the VT220 outputs the $charsetArray to the screen instead of setting it's memory.
			defineCharset(array());
			sleep(1);
			defineCharset($charsetArray);
			
			echo "done";
			
	        break;
			
	    case "testCharset":
			testCharset();
			echo "done";
	        break;
			
	    case "resetTerminal":
			resetTerminal();
			echo "done";
	        break;
			
	    case "setAsciiCharset":
			setCharset(asciiCharset);
			echo "done";
	        break;
		
	    case "setCustomCharset":
			setCharset(customCharset);
			echo "done";
	        break;
	    case "sendData":
			$data = $_POST['data'];
			sendData($data);
			echo "done";
	        break;
	}
}

function defineCharset($charArray){
	
	$str = '';
	// escape character
	$str .= chr(0x1b).chr(0x50);
	// set options and define target charset
	$str .= '1;1;1{'.customCharset;
		
	// define chars
	foreach($charArray as $char){
		$str .= asSixel($char);
	}
	// terminate character
	$str .= chr(0x1b).chr(0x5c);
	
	outputToDevice($str);
}

function setCharset($str){
	outputToDevice(chr(0x1b).chr(0x28).$str);
}

function testCharset(){
	$j = 0;
	for($i = 33; $i < 127 ; $i++){
		$j++;
		outputToDevice(chr($i));
		if($j % 12 == 0){
			outputToDevice("\r\n");
		}
	}
	outputToDevice("\r\n\r\n");
}

function resetTerminal(){
	outputToDevice(chr(0x1b).chr(0x63));
}

function outputToDevice($str){
	
	// rather hacky...
	// in php there is no library for setting up tty as far as my google results showed
   // we use now exec and "stty" for that
	// we also need a write delay, because without not all characters are received on the VT220 side.

	$output = null;
	$retval = null;
	exec('stty -F ' . device . ' ' . baudrate, $output, $retval);
   if($retval != 0) {
		echo 'Error setting up serial device';
		return;
	}
	
	$fp = fopen(device, 'w');
	if($fp) {
		for ($i = 0; $i < strlen($str); $i++) {
			fwrite($fp, $str[$i]);
			usleep(500);
		}
		fclose($fp);
	}
}

function asSixel($char){
	
	$sixelString = "";
	
	// do upper row	
	for($column = 0; $column < 8; $column++){
		$binaryString = "";
		for($i = 40; $i >= 0; $i = $i-8) {
			$binaryString .= $char[$i+$column];
		}
		$sixelString .= chr(bindec($binaryString)+63);
	}
	$sixelString .= "/";
	
	// do lower row
	for($column = 0; $column < 8; $column++){
		$binaryString = "";
		for($i = 72; $i >= 48; $i = $i-8) {
			$binaryString .= $char[$i+$column];
		}
		$sixelString .= chr(bindec($binaryString)+63);
	}
	
	$sixelString .= ";";
	
	return $sixelString;
}

function sendData($data){
	$data = preg_replace('~\R~u', "\r\n", $data);
	outputToDevice($data);
}

?>
