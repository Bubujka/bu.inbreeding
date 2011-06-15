<?php
$dog = tree(1);

p($dog->self);
p($dog->right->self);
p($dog->left->self);
p($dog->right->top->self);
p($dog->right->right->self);
p($dog->right->right->top->top->self);
---
Anne
Berd
Cinia
Anne
Danay
Anne
