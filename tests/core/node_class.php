<?
$n = node();
$n->right = "r";
$n->left = "l";
$n->top = "t";
$n->self = 'self';
echo $n->right.$n->left.$n->top.$n->self;
---
rltself
