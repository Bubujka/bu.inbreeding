<?php
$t = tree(3);
$arr = find_dups($t);
foreach($arr as $name=>$dups)
	p($name." - ".count($dups));
---
Кангу - 3
Кримсон - 3
Олан - 2
Сампан - 2
Чарнушка - 2
Чизкейк - 3

