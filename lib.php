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

function to_1d($tree){
	$left = array();
	$right = array();

	if($tree->left)
		$left = to_1d($tree->left);
	if($tree->right)
		$right = to_1d($tree->right);
	return array_merge(array($tree), $left, $right);
}

function find_dups($tree){
	$dups = array();
	foreach(to_1d($tree) as $node)
		$dups[$node->self][] = $node;
	foreach($dups as $k=>$v)
		if(count($v) == 1)
			unset($dups[$k]);
	ksort($dups);
	return $dups;
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