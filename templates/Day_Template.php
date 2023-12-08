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

class Day
{
    function __construct(private int $day) {
        $ex = 0;

        // The test data is so small we may as well just load both files in anyways
        $data_full = file_get_contents(__DIR__ . '/../../data/day_' . $day);
        $data_example = file_get_contents(__DIR__ . '/../../data/day_' . $day . '.ex');

        // $ex = 1;
        $data = $this->parse_input($ex === 1 ? $data_example : $data_full);
        
        // ====================================================================== //
        // ============================ Start Solving =========================== //
        // ====================================================================== //
        // Start the timer
        $overallTimer = new Timer();

        // Solve both parts at the same time. See solve() docblock for more info
        $solution = $this->solve($data);

        // Right answer: 
        Prenta::answer($solution[0], 1);

        // Right answer: 
        Prenta::answer($solution[1], 2);
 
        // Stop the timer
        $time_done = $overallTimer->stop();
        Prenta::time($time_done, 'Overall Time');
    }
    

    /**
     * Solves the problem
     * 
     * @param array $data The data to solve
     * @return array The solution to the problem in the form of [part1, part2]
     */
    protected function solve($data) {
        $part1 = 0;
        $part2 = 0;
        


        return [$part1, $part2];
    }

    protected function parse_input($data) {        
        $data = preg_split('/\r\n|\r|\n/', $data);



        return $data;
    }
}