<?php
// ----------------------------------------------------------------------------
// Problem description: https://adventofcode.com/2023/day/11
// Solution by: https://github.com/frhel (Fry)
// Part1: 9545480
// Part2: 406725732046
// ----------------------------------------------------------------------------
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use frhel\adventofcode2023php\Tools\Timer;
use frhel\adventofcode2023php\Tools\Prenta;
use frhel\adventofcode2023php\Tools\Utils;

class Day11 extends Day 
{
    function __construct(private int $day, $bench = 1000, $ex = 0) {
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

        [$nodes, $ex, $ey] = $data;

        $xs = [0,0];
        $ys = [0,0];
        $n_count = count($nodes);
        $j = $n_count;
        $md = 0;
        $add = 0;
        for ($i = 0; $i < $n_count - 2; $i++) {
            $j = $n_count;
            while ($j > $i) {
                $j--;
                $md = abs($nodes[$i][0] - $nodes[$j][0]) + abs($nodes[$i][1] - $nodes[$j][1]);
                $add = 0;
                
                $xs[0] = $nodes[$i][0];
                $xs[1] = $nodes[$j][0];
                $ys[0] = min($nodes[$i][1], $nodes[$j][1]);
                $ys[1] = max($nodes[$i][1], $nodes[$j][1]);
                foreach ($ex as $x) {
                    if ($x <= $xs[0]) continue;
                    else if ($x < $xs[1]) $add++;
                    else break;
                }
                foreach ($ey as $y) {
                    if ($y <= $ys[0]) continue;
                    else if ($y < $ys[1]) $add++;
                    else break;
                }

                $part1 += $md + $add;
                $part2 += $md + $add * 999999;
            }
        }

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

        $ex = []; // Empty rows
        for ($i = 0; $i < count($data); $i++) {
            $row = str_split(trim($data[$i]));
            if (!in_array('#', $row)) {
                $ex[] = $i;
            }
            $data[$i] = $row;
        }

        $ey = []; // Empty columns
        $galaxies = []; // Galaxy coordinates
        for ($i = 0; $i < count($data[0]); $i++) {
            $col = '';
            for ($j = 0; $j < count($data); $j++) {
                $col .= $data[$j][$i];
                if ($data[$j][$i] == '#') {
                    $galaxies[] = [$j, $i];
                }
            }
            if (!preg_match('/#/', $col)) {
                $ey[] = $i;
            }
        }

        sort($galaxies);
        return [$galaxies, $ex, $ey];
    }
}