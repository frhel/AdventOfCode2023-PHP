<?php
// ----------------------------------------------------------------------------
// Problem description: https://adventofcode.com/2023/day/12
// Solution by: https://github.com/frhel (Fry)
// Part1: 7402
// Part2: 3384337640277
// ----------------------------------------------------------------------------
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use frhel\adventofcode2023php\Tools\Timer;
use frhel\adventofcode2023php\Tools\Prenta;
use frhel\adventofcode2023php\Tools\Utils;

class Day12 extends Day 
{
    function __construct(private int $day, $bench = 3, $ex = 0) {
        // $ex = 1;
        parent::__construct($day, $bench, $ex);
    }
    

    /**
     * Solves the problem. Needs to be public so we can call it from the benchmarking code
     * 
     * @param array $data The data to solve
     * @return array The solution to the problem in the form of [part1, part2]
     */
    public function solve($data) {        
        $data = $this->parse_input($this->load_data($this->day, $this->ex)); $this->data = $data;
        $part1 = 0;
        $part2 = 0;

        foreach ($data as $instruction) {
            $part1 += $this->arrange($instruction['map'], $instruction['size']);
            $instruction['map'] = substr(str_repeat($instruction['map'].'?', 5), 0, -1) ;
            $instruction['size'] = explode(',', trim(str_repeat(implode(',', $instruction['size']).',', 5), ','));
            // print_r($instruction);
            $part2 += $this->arrange($instruction['map'], $instruction['size']);
        }
        

        return [$part1, $part2];
    }

    protected function arrange(string $map, array $springs, &$memo = []): int {
        $key = $map.'|'.implode(',', $springs);
        if (isset($memo[$key])) return $memo[$key];
        
        // Check if we have reached the end of the map
        if (strlen($map) === 0) return count($springs) === 0 ? 1 : 0;

        // If there is a "." Yeet the irrelevant data and recurse with a smaller map
        if ($map[0] === '.') 
            return $memo[$key] = $this->arrange(substr($map, 1), $springs, $memo);

        // If the first character in the map is a wildcard, we need to branch out
        // and try both options
        if ($map[0] === '?') return $memo[$key] =
                $this->arrange(substr_replace($map, '.', 0, 1), $springs, $memo)
                + $this->arrange(substr_replace($map, '#', 0, 1), $springs, $memo);

        // If the first character in the map is a "#" we can start trying to place things
        if ($map[0] === '#') {
            // Do we have any springs left to place on the map?
            if (count($springs) === 0) return 0;

            // Is there enough space left for all the springs?
            if (strlen($map) < array_sum($springs)) return 0;

            // Yeet if "." is found on top of the map segment that corresponds to the first spring size
            if (str_contains(substr($map, 0, (int) $springs[0]), '.')) return 0;

            // If we have more than one spring left to place, we need to solve for
            // those as well
            if (count($springs) > 1) {
                // No group can ever be followed by a "#". Yeet if we find one
                if ($map[$springs[0]] === '#') return 0;

                // If nothing prevents us from placing the first spring, we can recurse
                // Into the remaining springs. Slice the first spring plus its trailing
                // "." from the map and recurse with the remaining springs
                return $memo[$key] = $this->arrange(substr($map, $springs[0] + 1), array_slice($springs, 1), $memo);
            } else {
                // We are down to the last spring. Try placing it             
                return $memo[$key] = $this->arrange(substr($map, (int)$springs[0]), array_slice($springs, 1), $memo);;
            }
        }
    }

    /**
     * Parses the input data into a usable format
     * 
     * @param string $data The input data
     * @return array The parsed data
     */
    protected function parse_input($data) {        
        $data = preg_split('/\r\n|\r|\n/', $data);

        $out = [];
        foreach ($data as $key => $value) {
            $line = explode(' ', trim($value));
            $out[$key]['map'] = trim($line[0]);
            $out[$key]['size'] = explode(',', trim($line[1]));
        }

        return $out;
    }
}