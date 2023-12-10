<?php
// ----------------------------------------------------------------------------
// Problem description: https://adventofcode.com/2023/day/9
// Solution by: https://github.com/frhel (Fry)
// ----------------------------------------------------------------------------
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use frhel\adventofcode2023php\Tools\Timer;
use frhel\adventofcode2023php\Tools\Prenta;
use frhel\adventofcode2023php\Tools\Utils;

class Day9 extends Day
{
    function __construct(private int $day, $bench = 1000, $ex = 0) {     
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
        
        foreach ($data as $history) {
            $res = [$history];
            $track = [];
            while (count($track) !== 1) {
                $curr = &$res[array_key_last($res)];
                $track = [];
                $new = [];
                for ($key = 0; $key < count($curr) - 1; $key++) {
                    $val = $curr[$key + 1] - $curr[$key];
                    $new[] = $val;
                    $track[$val] = 0;
                }
                
                $res[] = $new;
            }

            $acc = 0;
            while($num = array_pop($res)) {
                $part1 += end($num);
                $acc = $num[0] - $acc;
            }
            $part2 += $acc;
        }

        return [&$part1, &$part2];
    }

    /*8
     * Parses the input data into a usable format
     * 
     * @param string $data The input data
     * @return array The parsed data
     */
    protected function parse_input($data) {        
        $data = preg_split('/\r\n|\r|\n/', $data);
        foreach ($data as $key => $line) {
            preg_match_all('/-*[0-9]\w*/', $line, $line);
            $data[$key] = $line[0];
        }

        return $data;
    }
}