<?php
$dups = find_meaningful_dups(tree(4));
foreach($dups as $k=>$v)
	echo $k." - ".count($v)."\n";

$first = array_shift($dups);
foreach($first as $v){
	echo $v->top->self."\n";
}

---
Бандама - 2
Груби - 2
Кримсон - 2
Германика
Мамона