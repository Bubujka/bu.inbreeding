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
	if(isset($tree['mom']))
		$node->left = mktree($node, $tree['mom']);
	if(isset($tree['dad']))
		$node->right = mktree($node, $tree['dad']);
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
	var $uid;
	function __construct($self=null){
		static $i = 0;
		$i++;
		$this->uid = $i;
		$this->self = $self;
	}

	function __toString(){
		return "<".$this->self.":".
			($this->top ? $this->top->self :'null').":".
			($this->left ? $this->left->self :'null').":".
			($this->right ? $this->right->self :'null').">";
	}

	function search($path){
		$t = array_reverse(explode(">", $path));
		$first = array_shift($t);
		$candidates = $this->all($first);
		foreach($candidates as $candidate){
			$parent = $candidate->top;
			foreach($t as $v){
				if($parent->self != $v)
					continue 2;
				$parent = $parent->top;
			}
			return $candidate;
		}
	}

	function all($name){
		$r = array();
		if($this->self == $name)
			$r[] = $this;
		return array_merge($r,
				   ($this->left ? $this->left->all($name) : array()),
				   ($this->right ? $this->right->all($name) : array()));
	}

	function to_1d(){
		return array_merge(array($this),
				   ($this->left ? $this->left->to_1d() : array()),
				   ($this->right ? $this->right->to_1d() : array()));
	}
}

class Triform{
	var $car;
	var $cdr;
	var $top;
	function __toString(){
		$r =  $this->top."\n";
		$r .= return_sort_by_top($this->car, $this->cdr)."\n\n";
		return $r;
	}
}

function print_triforms($triforms){
	foreach($triforms as $v){
		echo $v;
	}
}

function triforms($tree){
	$return = array();
	$grouped = array();

	foreach($tree->to_1d() as $v)
		$grouped[$v->self][] = $v;

	$all_pairs = $grouped;

	foreach($grouped as $k=>$v)
		if(count($v) == 1)
			unset($grouped[$k]);

	foreach($grouped as $k=>$v){
		foreach($v as $kk=>$vv){
			if(isset($grouped[$vv->top->self]))
				unset($grouped[$k][$kk]);
		}
	}

	foreach($grouped as $k=>$v){
		if(!count($v))
			unset($grouped[$k]);
	}

	foreach($grouped as $k=>$v){
		if(count($v) == 2){
			unset($grouped[$k]);
			$grouped[$k][] = pair(reset($v), end($v));
		}elseif(count($v) == 1){
			unset($grouped[$k]);
			$node = reset($v);
			foreach($all_pairs[$k] as $kk=>$vv){
				if($vv->uid != $node->uid){
					$grouped[$k][] = pair($node, $vv);
				}
			}
		}
	}

	foreach($grouped as $v){
		foreach($v as $vv){
			$t = triform();
			$t->top = find_common_node(reset($vv), end($vv));
			$t->car = reset($vv);
			$t->cdr = end($vv);
			$return[] = $t;
		}
	}

	return $return;
}

function pair($one, $two){
	return array($one, $two);
}
function top_self($v, $vv){
	$a = $v->top->self;
	$b = $vv->top->self;
	if ($a == $b) {
		return 0;
	}
	return ($a < $b) ? -1 : 1;

}

function return_sort_by_top(){
	$args = func_get_args();
	usort($args, 'top_self');
	return implode("\n", $args);
}

function triform(){
	return new Triform;
}

function p($wtf){
	echo $wtf."\n";
}

function node($self = null){
	return new Node($self);
}

function calc_distance($top, $first, $second){
	$i = 0;

	$p = $first->top;
	while($p->self != $top->self){
		$i++;
		$p = $p->top;
	}

	$p = $second->top;
	while($p->self != $top->self){
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
	$triforms = triforms($tree);
	$return = array();
	$skipped = array();
	$grouped = array();
	foreach($triforms as $k=>$v)
		$grouped[$v->top->self][] = $v;


	foreach($grouped as $k=>$v){
		if(count($v) == 1){
			$t = reset($v);
			if(isset($grouped[$t->car->self]) or
			   isset($grouped[$t->cdr->self])){
				$skipped[$k] = $v;
				continue;
			}
			$pk = pow(0.5, calc_distance($t->top, $t->car, $t->cdr));
			$k = (0.5 * $pk) * 100;
			$return[$t->top->self] = array('node'=>$t->top, 'num'=>$k);
		}
	}

	foreach($grouped as $k=>$v){
		if(count($v) != 1){
			$pk = 0;
			foreach($v as $t){
				if(isset($grouped[$t->car->self]) or
				   isset($grouped[$t->cdr->self])){
					$skipped[$k] = $v;
					continue 2;
				}
				$pk += pow(0.5, calc_distance($t->top, $t->car, $t->cdr));
			}
			$k = (0.5 * $pk) * 100;

			$t = reset($v);
			$return[$t->top->self] = array('node'=>$t->top, 'num'=>$k);
		}
	}

	foreach($skipped as $k=>$v){
		$pk = 0;
		foreach($v as $t){
			if(isset($grouped[$t->car->self])){
				$num = ($return[$t->car->self]['num'] / 100) + 1;
				$pk += (pow(0.5, calc_distance($t->top, $t->car, $t->cdr))) * $num;
			}
		}


		$k = (0.5 * $pk) * 100;
		$t = reset($v);

		$return[$t->top->self] = array('node'=>$t->top, 'num'=>$k);
	}
	/*
	foreach($grouped as $v){

		if(count($dups) == 1){
			$pk = pow(0.5, calc_distance($top, reset($dups)));

		}else{
			$pk = 0;
			foreach($dups as $vv)
				$pk += pow(0.5, calc_distance($top, $vv));
		}
	}

	*/
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


