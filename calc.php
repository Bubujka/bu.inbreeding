#!/usr/bin/env php
<?php
$id = $argv[1];
require_once 'lib.php';
print_inbreeding(calc_inbreeding(tree($id)));
