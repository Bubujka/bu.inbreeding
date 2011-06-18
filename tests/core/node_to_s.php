<?php
$n = node();
$n->self = 'waserd';
echo $n."\n";

$n = node();
$n->self = 'bubujka';
$n->top = node('t');
$n->car = node('l');
$n->cdr = node('r');
echo $n;
---
<waserd:null:null:null>
<bubujka:t:l:r>
