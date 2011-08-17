<?php
/*
How to integrate bu.inbreeding to your project.
1. Include it
2. Write translater from your`s tree - to our tree.
3. Call calc_inbreeding() with translated tree
4. Print results;
*/
require_once 'lib.php';

$our_tree = array('id'=>1,
		  'mother'=>array('id'=>2),
		  'father'=>array('id'=>3,
				  'mother'=>array('id'=>2)));

function translate($tree, $top = null){
	$node = node();
	$node->self = $tree['id'];
	$node->top = $top;

	if(isset($tree['mother']))
		$node->car = translate($tree['mother'], $node);
	if(isset($tree['father']))
		$node->cdr = translate($tree['father'], $node);
	return $node;
}

foreach(calc_inbreeding(translate($our_tree)) as $v)
	echo "Object with id: ".$v['node']->self.' have COI: '.$v['num']."%\n";

