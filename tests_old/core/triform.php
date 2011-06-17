<?php
$t = triform();

$t->top = "a";
$t->car = "b";
$t->cdr = "c";
echo $t->cdr.$t->car.$t->top;
---
cba
