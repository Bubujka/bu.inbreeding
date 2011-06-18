<?
$n = node();
$n->cdr = "r";
$n->car = "l";
$n->top = "t";
$n->self = 'self';
echo $n->cdr.$n->car.$n->top.$n->self;
---
rltself
