<?php
$dog = tree(1);

p($dog->self);
p($dog->cdr->self);
p($dog->car->self);
p($dog->cdr->top->self);
p($dog->cdr->cdr->self);
p($dog->cdr->cdr->top->top->self);
---
Anne
Berd
Cinia
Anne
Danay
Anne
