<?php
$tree = tree(3);
$dups = find_meaningful_dups($tree);
$dup = reset($dups);
echo find_common_node(reset($dup), end($dup))->self;
---
Полина
