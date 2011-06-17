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

class Triform{
	var $car;
	var $cdr;
	var $top;
}


function triform(){
	return new Triform;
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

function find_common_node($f, $s){
	$dog_parent = $f->top;

	while($dog_parent){

		$candidate_parent = $s->top;

		while($candidate_parent){
			if($candidate_parent->self == $dog_parent->self)
				return $candidate_parent;

			$candidate_parent = $candidate_parent->top;
		}
		$dog_parent = $dog_parent->top;
	}

}

function hr(){
	p('-------------------------');
}
function pr($v){
	p('self: '.$v->self);
	p('top: '.$v->top->self);
	p("__");
}
function pak($arr){
	print_R(array_keys($arr));
}

function calc_imbriding($tree){
	$dups = find_meaningful_dups($tree);

	$return = array();
	$grouped = array();
	foreach($dups as $dup){
		$top = find_common_node(reset($dup), end($dup));
		$grouped[$top->self]['top'] = $top;
		$grouped[$top->self]['dups'][] = $dup;
	}


	foreach($grouped as $v){
		$top = $v['top'];
		$dups = $v['dups'];

		if(count($dups) == 1){
			$pk = pow(0.5, calc_distance($top, reset($dups)));

		}else{
			$pk = 0;
			foreach($dups as $vv)
				$pk += pow(0.5, calc_distance($top, $vv));
		}
		$k = (0.5 * $pk) * 100;
		$return[$top->self] = array('node'=>$top, 'num'=>$k);
	}
	ksort($return);


	return $return;
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

	$candidates = array();
	foreach($variants as $k=>$v){
		if($v->top->self == $dog->top->self)
			continue;
		$dog_parent = $dog->top;

		$candidate = $v;
		$level = 0;
		while($dog_parent){
			$candidate_parent = $v->top;
			while($candidate_parent){
				if($candidate_parent === $dog_parent)
					$candidates[$level] = $candidate;
				$candidate_parent = $candidate_parent->top;
			}
			$level++;
			$dog_parent = $dog_parent->top;
		}
	}
	ksort($candidates);
	return reset($candidates);
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


