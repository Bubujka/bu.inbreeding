<?php
foreach(glob(dirname(__FILE__).'/helpers/*') as $v)
	require_once $v;

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

function tree($n){
	$raw_tree = unyaml_file('trees/'.$n.'.yaml');
	return mktree(null, $raw_tree);
}

function mktree($top, $tree){
	$node = node();
	$node->self = $tree['name'];
	$node->top = $top;
	if(isset($tree['mom']))
		$node->car = mktree($node, $tree['mom']);
	if(isset($tree['dad']))
		$node->cdr = mktree($node, $tree['dad']);
	return $node;
}

class Node{
	var $car;
	var $cdr;
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
			($this->car ? $this->car->self :'null').":".
			($this->cdr ? $this->cdr->self :'null').">";
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
				   ($this->car ? $this->car->all($name) : array()),
				   ($this->cdr ? $this->cdr->all($name) : array()));
	}

	function to_1d(){
		return array_merge(array($this),
				   ($this->car ? $this->car->to_1d() : array()),
				   ($this->cdr ? $this->cdr->to_1d() : array()));
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
	// pk - ничего не значит. Просто 2 буквы.
	function pk(){
		return pow(0.5, calc_distance($this->top, $this->car, $this->cdr));
	}
}

function triform(){
	return new Triform;
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
			$grouped[$k][] = cons(reset($v), end($v));
		}elseif(count($v) == 1){
			unset($grouped[$k]);
			$node = reset($v);
			foreach($all_pairs[$k] as $kk=>$vv){
				if($vv->uid != $node->uid){
					$grouped[$k][] = cons($node, $vv);
				}
			}
		}else{
			unset($grouped[$k]);
			$grouped[$k] = pairs($v);
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

function pairs($array){
	//составляет все возможные комбинации из этого массива
	$r = array();
	$cnt = count($array);

	foreach($array as $k=>$v){
		$t = array();

		for($i = $k+1 ; $i < $cnt; $i++){
			$t = array($v);
			$t[] = $array[$i];
			$r[] = $t;
		}
	}
	return $r;
}


function node($self = null){
	return new Node($self);
}

function calc_distance($top, $first, $second){
	$i = 0;

	$p = $first->top;
	while($p->uid != $top->uid){
		$i++;
		$p = $p->top;
	}

	$p = $second->top;
	while($p->uid != $top->uid){
		$i++;
		$p = $p->top;
	}

	return $i;
}

function find_common_node($one, $two){
	$one_top = $one->top;
	while($one_top){
		$two_top = $two->top;
		while($two_top){
			if($two_top->uid == $one_top->uid)
				return $two_top;
			$two_top = $two_top->top;
		}
		$one_top = $one_top->top;
	}
}

function calc_inbreeding($tree){
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
			$k = (0.5 * $t->pk()) * 100;
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
				$pk += $t->pk();
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
				$pk += $t->pk() * $num;
			}
		}
		$k = (0.5 * $pk) * 100;
		$t = reset($v);
		$return[$t->top->self] = array('node'=>$t->top, 'num'=>$k);
	}

	ksort($return);
	return $return;
}
