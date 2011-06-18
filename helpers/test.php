<?php

function print_imbriding($data){
	foreach($data as $v)
		echo $v['node']->self." - ".$v['num']."\n";
}

function p($wtf){
	echo $wtf."\n";
}

function print_triforms($triforms){
	foreach($triforms as $v)
		echo $v;
}
