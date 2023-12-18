<?php

namespace frhel\adventofcode2023php\Tools;

use SplPriorityQueue;

class MinPriorityQueue extends SplPriorityQueue {
    public function compare(mixed $priority1, mixed $priority2):int  {
        if ($priority1 === $priority2) return 0;
        return $priority2 > $priority1 ? 1 : -1;
    }
}