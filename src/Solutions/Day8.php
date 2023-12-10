<?php
// ----------------------------------------------------------------------------
// Problem description: https://adventofcode.com/2023/day/8
// Solution by: https://github.com/frhel (Fry)
// ----------------------------------------------------------------------------
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use frhel\adventofcode2023php\Tools\Timer;
use frhel\adventofcode2023php\Tools\Prenta;
use frhel\adventofcode2023php\Tools\Utils;


class Day8 extends Day
{
    function __construct(private int $day, $bench = 100, $ex = 0) {     
        parent::__construct($day, $bench, $ex);
    }    

    /**
     * Solves the problem
     * 
     * @param array $data The data to solve
     * @return array Solution tuple in the form of [part1, part2]
     */
    public function solve($data) {
        $utils = new Utils();
        [$part1, $part2] = [0, 0];
        $count = 0;
    
        // Start solving part 1
        $part1 = $this->find_shortest_path($data['nodes'], 'AAA', $data['instr'], 'ZZZ');

        // Start solving part 2
        // Loop over the nodes array and process all the nodes that end in Z
        $shortest_paths = [];
        foreach ($data['nodes'] as $key => $node) {
            $count++;
            if ($key[2] === 'A') {
                $shortest_paths[] = $this->find_shortest_path($data['nodes'], $key, $data['instr'], "..Z");
            }
        }
        // Calculate the Least Common Multiple of all the numbers in the array
        // Moved the LCM and GCD methods to the Utils class, but left the old
        // code below for reference
        $part2 = $utils->lcm($shortest_paths); 

        return [$part1, $part2];
    }

    protected function find_shortest_path($nodes, $start_node, $instr, $target) {
        $curr_node = $start_node;
        $idx = 0;
        // While preg_match doesn't find the target keep going
        while (!preg_match('/' . $target . '/', $curr_node)) {
            $curr_instr = $instr[$idx % count($instr)];
            $curr_node = $curr_instr === 'L' ? $nodes[$curr_node]['left'] : $nodes[$curr_node]['right'];
            $idx++;
        }
        return $idx;
    }

    /**
     * Calculates the Least Common Multiple of all the numbers in the array
     * 
     * @param array $arr The array of numbers
     * @return int The Least Common Multiple of all the numbers in the array
     */
    public function lcm($arr) {
        // Reduce the array by multiplying all the numbers together and dividing by the Greatest Common Divisor
        // of the current number and the accumulator
        return array_reduce($arr, fn($carry, $item) => ($carry * $item) / $this->gcd($carry, $item), 1);
    }

    /**
     * Calculates the Greatest Common Divisor of two numbers
     * 
     * @param int $a First number
     * @param int $b Second number
     * @return int The Greatest Common Divisor of the two numbers
     */
    public function gcd($a, $b) {
        // Make sure $a is the smaller number
        [$a, $b] = $a > $b ? [$b, $a] : [$a, $b];
        
        // Literally just loop over all the numbers from 2 to $b and check if they divide into both $a and $b
        for ($gcd = 2; $gcd <= $b; $gcd++) {
            if ($a % $gcd === 0 && $b % $gcd === 0) return $gcd;
        }
        return 1;
    }

    /**
     * Parses the input data into a usable format
     * 
     * @param string $data The input data
     * @return array The parsed data
     */
    protected function parse_input($data) {        
        $data = preg_split('/\r\n|\r|\n/', $data);

        $out['instr'] = str_split(trim($data[0]));

        unset($data[0], $data[1]);
        $data = array_values($data);

        foreach ($data as $line) {
            $node = explode(' = ', $line)[0];
            [   $out['nodes'][$node]['left'],
                $out['nodes'][$node]['right']
            ] = explode(', ', explode(')', explode('(', $line)[1])[0]);
        }

        return $out;
    }
}