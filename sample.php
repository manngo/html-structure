<?php
	require_once 'html-structure.php';

	$text=file_get_contents('sample.txt');
	$text=preg_split('/\r?\n/',$text);

	//	alternatively
	
	$text=file('sample.txt');

print HTMLStructure::make($text,true);

