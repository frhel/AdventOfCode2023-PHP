<?php
// ----------------------------------------------------------------------------
// Problem description: https://adventofcode.com/2023/day/
// Solution by: https://github.com/frhel (Fry)
// ----------------------------------------------------------------------------
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use frhel\adventofcode2023php\Tools\Timer;
use frhel\adventofcode2023php\Tools\Prenta;
use frhel\adventofcode2023php\Tools\Utils;

class Day_Template extends Day 
{
    function __construct(private int $day, $bench = 0, $ex = 0) {
        $ex = 1;
        parent::__construct($day, $bench, $ex);
    }
    

    /**
     * Solves the problem. Needs to be public so we can call it from the benchmarking code
     * 
     * @param array $data The data to solve
     * @return array The solution to the problem in the form of [part1, part2]
     */
    public function solve($data) {
        $part1 = 0;
        $part2 = 0;
        

        return [$part1, $part2];
    }

    /**
     * Parses the input data into a usable format
     * 
     * @param string $data The input data
     * @return array The parsed data
     */
    protected function parse_input($data) {        
        $data = preg_split('/\r\n|\r|\n/', $data);


        return $data;
    }
}