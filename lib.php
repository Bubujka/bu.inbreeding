<?php
require 'spyc.php';
function unyaml($string){
	return Spyc::YAMLLoadString($string);
}

function unyaml_file($file){
	return unyaml(file_get_contents($file));
}

function tree($n){
	return unyaml_file('trees/'.$n.'.yaml');
}