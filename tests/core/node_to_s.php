<?php
$n = node();
$n->self = 'waserd';
echo $n."\n";

$n = node();
$n->self = 'bubujka';
$n->top = node('t');
$n->left = node('l');
$n->right = node('r');
echo $n;
---
<waserd:null:null:null>
<bubujka:t:l:r>
