<?php

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

// Exits with a print_r and call trace for debugging
// $hidden inits array output into browser console
// Backtrace: jurchiks101 at gmail dot com - http://php.net/manual/en/function.debug-backtrace.php#112238
if (!function_exists('x_r')) {
	function x_r($obj, $exit = true, $return = true, $hidden = false, $console_msg = '') {
		
		// include a debug call trace
		$e = new Exception();
		$trace = explode("\n", $e->getTraceAsString());
		
		// reverse array to make steps line up chronologically
		$trace = array_reverse($trace);
		array_shift($trace); // remove {main}
		//array_pop($trace); // remove call to this method
		$length = count($trace);
		$result = array();
		
		for ($i = 0; $i < $length; $i++) {
			$result[] = ($i + 1)  . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
		}
		
		// mitigate exit or console output
		if ($hidden === true) {
			$jsond = str_replace("'", "\'", json_encode(array_values($obj)));
			$output = "<script type='text/javascript'>";
			$output .= "var obj = JSON.parse('" . $jsond . "'),";
			$output .= "	cond_css_head = 'font-weight: bold; font-size: 1.2em; letter-spacing: -1; color: #658500;',";
			$output .= "	cond_css_row = 'font-style: italic; color: #696969;';";
			$output .= 'console.log("%c<consoled>", cond_css_head);';
			if (!empty($console_msg)) {
				$output .= 'console.log("%c // ' . $console_msg . '", cond_css_row);';
			} else {
				$output .= 'console.log("%c // output from x_r()", cond_css_row);';
			}
			$output .= "console.dir(obj);";
			$output .= 'console.log("%c</consoled>", cond_css_head);';
			$output .= "</script>";
			
			echo $output;
		} else {
			echo '<pre style="background: #FFFFFF;">', print_r($obj, $return), '</pre>';
		}
		
		// mitigate call trace
		if ($hidden === false) {
			echo '<pre style="background: #FFFFFF;">', print_r($result, $return), '</pre>';
		}
		
		// send a notice to syslog in case we forget x_r() is running ;)
		syslog(LOG_NOTICE, 'x_r() function init at: ' . end($result));
		
		// mitigate exit
		if ($exit === true) {
			exit();
		}
	}
}

// Dumps to a file. Can be used with x_r() in circumstances where displaying vars, exiting, or console logging is not desirable.
// $root_directory is the path to the folder where you would like the file to be placed
if (!function_exists('x_dump')) {
	function x_dump($message, $append = false, $name = 'x_dump.txt') {
		$file = $root_directory . $name;
		
		if ($append) {
			$handle = fopen($file, 'a+');
		} else {
			$handle = fopen($file, 'w');
		}
		
		$message = print_r($message, 1);
		
		fwrite($handle, $message);
			
		fclose($handle);
	}
}