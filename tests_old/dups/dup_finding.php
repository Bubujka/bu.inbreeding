<?php
$t = tree(5);
$arr = find_dups($t);
foreach($arr as $name=>$dups)
	p($name." - ".count($dups));
---
Ангор - 2
Битл Джус - 2
Сампан - 2
Тамбливид - 2
