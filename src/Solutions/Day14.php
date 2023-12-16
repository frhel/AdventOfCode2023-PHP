<?php
// ----------------------------------------------------------------------------
// Problem description: https://adventofcode.com/2023/day/14
// Solution by: https://github.com/frhel (Fry)
// Part1: 106990
// Part2: 100531
// ----------------------------------------------------------------------------
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use frhel\adventofcode2023php\Tools\Timer;
use frhel\adventofcode2023php\Tools\Prenta;
use frhel\adventofcode2023php\Tools\Utils;

class Day14 extends Day
{
    private array $dir;
    function __construct(private int $day, $bench = 10, $ex = 0) {
        // $ex = 1;
        // Just a quick way to get the directions for the grid increments
        $this->dir = [
            'n' => ['row' => 0, 'col' => -1],
            'w' => ['row' => -1, 'col' => 0],
            's' => ['row' => 0, 'col' => 1],
            'e' => ['row' => 1, 'col' => 0],
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

        $part1 = $this->calc_load($this->shift_grid($grid, 'n'));
        $part2 = $this->cycle_tilt($grid, 1000000000);

        return [$part1, $part2];
    }

    /**
     * Finds the load of the north edge of the grid after the given number of cycles
     * Uses cycle detection to speed up the process
     *
     * @param array $grid The grid to calculate the load of
     * @param int $cycles The number of cycles to run
     * @return int The load of the grid
     */
    private function cycle_tilt($grid, $cycles) {
        $memo = [];
        // Run all the cycles
        for ($i = 0; $i < $cycles; $i++) {
            $grid = $this->cycle($grid);

            // Create a key for the cycle detection
            $key = implode('', array_map(function($row) {
                return implode('', $row);
            }, $grid));

            // Detect cycle and calculate the remaining cycles
            if (isset($memo[$key])) {
                $cycle_len = $i - $memo[$key];
                $cycle_nr = $i + 1;
                break;
            } else $memo[$key] = $i;
        }

        // Calculate the number of cycles left to reach the target
        $cycles = ($cycles - $cycle_nr) % $cycle_len;

        // Run the remaining cycles
        for ($i = 0; $i < $cycles; $i++)
            $grid = $this->cycle($grid);

        return $this->calc_load($grid);
    }

    /**
     * Runs one cycle of the grid. Mostly just to keep things tidy
     * and avoid too much nesting in the cycle_tilt and shift_grid methods
     *
     * @param array $grid The grid to run the cycle on
     * @return array The updated grid
     */
    private function cycle($grid) {
        foreach ($this->dir as $dir => $d)
            $grid = $this->shift_grid($grid, $dir);
        return $grid;
    }

    /**
     * Shifts the rocks in the grid in the given direction
     *
     * @param array $grid The grid to shift
     * @param string $dir The direction to shift the grid in
     * @return array The shifted grid
     */
    private function shift_grid($grid, $dir) {
        // We can reuse the same loop mods for both north and west, and another set for south and east
        // Just trying to find a way here to cut down on cycles
        $mod = $dir === 'n' || $dir === 'w' ? 1 : -1;
        $start = $mod > 0 ? 0 : count($grid) - 1;
        $bound = $mod > 0 ? count($grid) : -1;
        $step = 0 + $mod;

        for ($col = $start; $col !== $bound; $col += $step)
            for ($row = $start; $row !== $bound; $row += $step)
                // Move the rock if it's round
                if ($grid[$col][$row] === 'O') $grid = $this->move_rock($grid, $col, $row, $dir);
        return $grid;
    }

    // Actually moves the rock in the given direction
    private function move_rock($grid, $col, $row, $dir) {
        // Saving the starting positions so we can do a swap later
        $s_col = $col;    $s_row = $row;
        // Increment the position until we hit an edge or another rock
        while (true) {
            $n_col = $col + $this->dir[$dir]['col'];
            $n_row = $row + $this->dir[$dir]['row'];
            if ($this->check_bounds($grid, $n_col, $n_row) && $grid[$n_col][$n_row] === '.') {
                $col = $n_col;
                $row = $n_row;
            } else { break; }
        }

        // If we haven't moved there's no need to swap
        if ($col === $s_col && $row === $s_row) return $grid;

        // Swap the rock with the empty space
        $grid[$col][$row] = 'O';
        $grid[$s_col][$s_row] = '.';

        return $grid;
    }

    /**
     * Calculates the load of the north side of the grid
     *
     * @param array $grid The grid to calculate the load of
     * @return int The load of the grid
     */
    private function calc_load($grid) {
        $load = 0;
        foreach ($grid as $col => $row) {
            $counts = array_count_values($row);
            $load += isset($counts['O']) ? $counts['O'] * (count($grid) - $col) : 0;
        }
        return $load;
    }



    private function check_bounds($grid, $col, $row) {
        return $col >= 0 && $col < count($grid) && $row >= 0 && $row < count($grid[$col]);
    }

    private function print_grid($grid) {
        array_map(function($row) {
            echo implode('', $row) . PHP_EOL;
        }, $grid);
        echo PHP_EOL;
    }

    /**
     * Parses the input data into a usable format
     *
     * @param string $data The input data
     * @return array The parsed data
     */
    protected function parse_input($data) {
        $data = preg_split('/\r\n|\r|\n/', $data);

        return array_map(function($row) {
            return str_split($row);
        }, $data);
    }
}