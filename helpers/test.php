<?php

function print_inbreeding($data){
	foreach($data as $v)
		echo $v['node']->self." - ".$v['num']."\n";
}

function print_triforms($triforms){
	foreach($triforms as $v)
		echo $v;
}
