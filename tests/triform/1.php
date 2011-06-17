<?php
$t = triforms(tree(1));
$t = reset($t);
echo $t->top."\n";
print_sort_by_top($t->car, $t->cdr);
---
<Anne:null:Cinia:Berd>
<Neo:Ford:Danaya F:Cinos T>
<Neo:Jiro:null:null>

