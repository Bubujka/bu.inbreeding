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

function calc_distance($tree, $dups){
	$i = 0;

	$first = array_shift($dups);
	$second = array_shift($dups);
	$p = $first->top;
	while($p->self != $tree->self){
		$i++;
		$p = $p->top;
	}

	$p = $second->top;
	while($p->self != $tree->self){
		$i++;
		$p = $p->top;
	}

	return $i;
}

function nearest_common_node(){

}

function calc_imbriding($tree){
	$dups = find_dups($tree);

	//	foreach(

	//$k = (0.5 * pow(0.5, calc_distance(nearest_common_node($tree, array_shift($dups))))) * 100;

	return array(array('node'=>$tree,
			   'num'=>$k));
}

function print_imbriding($data){
	foreach($data as $v)
		echo $v['node']->self." - ".$v['num']."\n";
}

function remove_dup_parents($dups){
	foreach($dups as $k=>$v){
		foreach($v as $kk=>$vv){
			if(isset($dups[$vv->top->self]))
				unset($dups[$k][$kk]);
		}
	}

	foreach($dups as $k=>$v)
		if(!count($v))
			unset($dups[$k]);

	return $dups;
}

function find_nearest_pair($dups, $dog){
	$variants = $dups[$dog->self];
	foreach($variants as $k=>$v){
		if($v->top->self == $dog->top->self)
			continue;
		$dog_parent = $dog->top;

		$candidate = $v;

		while($dog_parent){
			$candidate_parent = $v->top;
			while($candidate_parent){
				if($candidate_parent->self == $dog_parent->self)
					return $candidate;
				$candidate_parent = $candidate_parent->top;
			}
			$dog_parent = $dog_parent->top;
		}
	}
}

function fill_with_missed_pair($dups, $cleared_dups){
	foreach($cleared_dups as $k=>$v)
		if(count($v) == 1)
			$cleared_dups[$k][] = find_nearest_pair($dups, reset($v));
	return $cleared_dups;
}

function find_meaningful_dups($tree){
	$dups = find_dups($tree);
	$cleared_dups = remove_dup_parents($dups);
	return fill_with_missed_pair($dups, $cleared_dups);
}

