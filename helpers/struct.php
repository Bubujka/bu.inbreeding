<?php


function cons($one, $two){
	return array($one, $two);
}

function car($cons){
	return reset($cons);
}

function cdr($cons){
	return end($cons);
}
