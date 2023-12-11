<?php

namespace frhel\adventofcode2023php\Solutions;

use frhel\adventofcode2023php\Tools\Timer;
use frhel\adventofcode2023php\Tools\Prenta;
use frhel\adventofcode2023php\Tools\Utils;

// find all the days
$days = [];
$files = scandir(__DIR__);
foreach ($files as $file) {
    if (preg_match('/^Day\d+\.php$/', $file)) {
        $day = (int) str_replace(['Day', '.php'], '', $file);
        $days[$day] = $file;
    }
}

// load all the days
foreach ($days as $day => $file) {
    require_once __DIR__ . '/' . $file;
}