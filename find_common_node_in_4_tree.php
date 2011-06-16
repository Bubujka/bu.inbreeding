<?php
$tree = tree(4);
$dups = find_meaningful_dups($tree);
$r = array();
foreach($dups as $dup)
	$r[] = find_common_node(reset($dup), end($dup))->self;
sort($r);
foreach($r as $v)
	p($v);
---
Горциза
Мастер Кард
Мастер Кард
