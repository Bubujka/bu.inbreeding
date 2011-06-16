<?php
function d($w){
	echo $w->self."\n";
}
$tree = tree(4);
$dups = find_meaningful_dups($tree);
$dup = reset($dups);

$f = reset($dup);
$s = end($dup);


d($f->top);
d($f->top->top);
d($f->top->top->top);
d($f->top->top->top->top);
echo "-\n";
d($s->top);
d($s->top->top);
d($s->top->top->top);
d($s->top->top->top->top);
---
Германика
Валакуента
Мастер Кард
Горциза
-
Мамона
Груби
Мастер Кард
Горциза


