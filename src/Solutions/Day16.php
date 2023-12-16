<?php
// ----------------------------------------------------------------------------
// Problem description: https://adventofcode.com/2023/day/16
// Solution by: https://github.com/frhel (Fry)
// Part1:
// Part2:
// ----------------------------------------------------------------------------
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use frhel\adventofcode2023php\Tools\Timer;
use frhel\adventofcode2023php\Tools\Prenta;
use frhel\adventofcode2023php\Tools\Utils;

class Day16 extends Day
{
    private $beams;
    private $beam;
    private $tiles;
    private $visited;
    private $dir;
    function __construct(private int $day, $bench = 0, $ex = 0) {
        $ex = 1;

        $this->beams = [];
        $this->beam = [
            'pos' => [0,0],
            'strpos' => '0,0',
            'dir' => 'E',
            'axis' => 'X',
        ];
        $this->visited = [];
        $this->dir = [
            'N' => [0,-1],
            'E' => [1,0],
            'S' => [0,1],
            'W' => [-1,0]
        ];

        parent::__construct($day, $bench, $ex);
    }


    /**
     * Solves the problem. Needs to be public so we can call it from the benchmarking code
     *
     * @param array $data The data to solve
     * @return array The solution to the problem in the form of [part1, part2]
     */
    public function solve($data, $part1 = 0, $part2 = 0) {
        $grid = $this->parse_input($this->load_data($this->day, $this->ex)); $this->data = $data;

        $stack = [$this->beam];
        $this->visited = [];
        while (count($stack) > 0) {
            $beam = array_pop($stack);
            $this->visited[$beam['strpos']] = 1;
            while (true) {
                $beam = $this->move($grid, $beam);
                // What did we hit?
                $tile = $grid[$beam['pos'][1]][$beam['pos'][0]];
                $res = $this->process_tile($beam, $tile);
                break;
            }
        }
        return [$part1, $part2];
    }

    private function process_tile($beam, $tile) {
        switch ($tile) {
            case '|':
                // Continue moving
                break;
            case '-':
                // Continue moving
                break;
            case '/':
                // Change direction
                break;
            case '\\':
                // Change direction
                break;
            case '+':
                // Change direction
                break;
            case ' ':
                // Stop
                break;
            default:
                // Stop
                break;
        }
    }

    private function move($grid, $beam) {
        // Increment the position until we hit an edge or another rock
        while (true) {
            $n_col = $beam['pos'][1] + $this->dir[$beam['dir']][1];
            $n_row = $beam['pos'][0] + $this->dir[$beam['dir']][0];
            if ($this->check_bounds($grid, $n_col, $n_row) && $grid[$n_col][$n_row] === '.') {
                $this->visited[$n_row . ',' . $n_col] = 1;
                $beam['pos'] = [$n_row, $n_col];
                // What did we hit
            } else { break; }
        }

        // We have hit something! Return the new position
        $this->visited[$n_row . ',' . $n_col] = 1;
        $beam['pos'] = [$n_row, $n_col];

        return $beam;
    }

    private function check_bounds($grid, $col, $row) {
        return $col >= 0 && $col < count($grid) && $row >= 0 && $row < strlen($grid[$col]);
    }

    /**
     * Parses the input data into a usable format
     *
     * @param string $data The input data
     * @return array The parsed data
     */
    protected function parse_input($data) {
        $data = preg_split('/\r\n|\r|\n/', $data);

        $data = array_map(function($row) {
            return trim($row);
        }, $data);

        return $data;
    }
}