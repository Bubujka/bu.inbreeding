<?php
require 'spyc.php';
function unyaml($string){
	return Spyc::YAMLLoadString($string);
}

function unyaml_file($file){
	return unyaml(file_get_contents($file));
}

function tree($n){
	$raw_tree = unyaml_file('trees/'.$n.'.yaml');
	return mktree(null, $raw_tree);
}

function mktree($top, $tree){
	$node = node();
	$node->self = $tree['name'];
	$node->top = $top;
	if(isset($tree['mother']))
		$node->left = mktree($node, $tree['mother']);
	if(isset($tree['father']))
		$node->right = mktree($node, $tree['father']);
	return $node;
}

class Node{
	var $left;
	var $right;
	var $top;
	var $self;
}

function p($wtf){
	echo $wtf."\n";
}

function node(){
	return new Node;
}