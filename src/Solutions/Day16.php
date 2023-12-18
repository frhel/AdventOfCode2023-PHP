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
    private $stack;
    private $cc_map;
    private $c_map;
    private $axis;
    private $beam_nr;
    private $fs_map;
    private $bs_map;

    function __construct(private int $day, $bench = 0, $ex = 0) {

        // $ex = 1;
        $this->stack = [];
        $this->visited = [];
        $this->beam = [];

           $this->dir = ['N' => [0,-1], 'E' => [1,0], 'S' => [0,1], 'W' => [-1,0]];
        $this->cc_map = ['N' => 'W', 'W' => 'S', 'S' => 'E', 'E' => 'N'];
         $this->c_map = ['N' => 'E', 'E' => 'S', 'S' => 'W', 'W' => 'N'];
         $this->fs_map = ['N' => 'E', 'W' => 'S', 'S' => 'W', 'E' => 'N'];
         $this->bs_map = ['N' => 'W', 'E' => 'S', 'S' => 'E', 'W' => 'N'];
          $this->axis = ['E' => 'X', 'W' => 'X', 'N' => 'Y', 'S' => 'Y'];

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

        $part1 = $this->count_unique_visits($this->shoot_beam($grid, 0, 1, 'E'));
        $part2 = $this->FIRE_ALL_ZE_LAZERS($grid);

        // $this->print_visited($grid);
        return [$part1, $part2];
    }

    private function FIRE_ALL_ZE_LAZERS($grid) {
        $results = [];
        for ($y = 1; $y < count($grid) - 1; $y++) {
            $results[] = $this->count_unique_visits($this->shoot_beam($grid, 0, $y, 'E'));
            $results[] = $this->count_unique_visits($this->shoot_beam($grid, strlen($grid[$y])-1, $y, 'W'));
        }
        for ($x = 1; $x < strlen($grid[$y]) - 1; $x++) {
            $results[] = $this->count_unique_visits($this->shoot_beam($grid, $x, 0, 'S'));
            $results[] = $this->count_unique_visits($this->shoot_beam($grid, $x, count($grid)-1, 'N'));
        }
        return max($results);
    }

    private function shoot_beam($grid, $x, $y, $dir) {
        $this->beam = [
            'pos' => [$x, $y],
            'dir' => $dir,
        ];
        // Making the stack a property of the class so we can access it from the process_tile method
        $this->stack = [$this->beam];
        $this->visited = [];
        while (count($this->stack) > 0) {
            $beam = array_pop($this->stack);
            // Log the starting position of the new beam to the visited array
            $this->visited[implode(',', $beam['pos'])] = $beam['pos'];
            while (true) {
                // Start by moving to a new tile
                $beam['pos'] = [$beam['pos'][0] + $this->dir[$beam['dir']][0], $beam['pos'][1] + $this->dir[$beam['dir']][1]];

                // Break on hitting an outer boundary
                if ($grid[$beam['pos'][1]][$beam['pos'][0]] === 'X') break;

                // Break on hitting a tile that has already been visited from this direction
                // because it means we are in a loop or going to end up following a path we have already followed
                $key = implode(',', $beam['pos']).$beam['dir'];
                if (isset($this->visited[$key])) break;
                $this->visited[$key] = $beam['pos']; // Add the new position to the visited array

                // Break on hitting a tile that we have already visited from the same direction before
                if ($grid[$beam['pos'][1]][$beam['pos'][0]] === '.') continue;

                // Process the tile we are on and act according to the rules
                $beam = $this->process_tile($grid, $beam, $x, $y);

                if ($beam === 'done') break;
            }
        }

        return $this->visited;
    }

    private function count_unique_visits($visited) {
        // Remove the first element from the array because it is the starting position and is outside the grid
        unset($visited[array_key_first($visited)]);
        $unique = [];
        foreach ($visited as $k => $v) $unique[$v[1].','.$v[0]] = 0;
        return count($unique);
    }

    private function print_visited($grid) {
        $grid_c = $grid;
        foreach($this->visited as $k => $v) {
            $grid_c[$v[1]][$v[0]] = '#';
        }
        Utils::print_grid($grid_c);
    }

    private function process_tile($grid, $beam, $x, $y) {
        switch ($grid[$beam['pos'][1]][$beam['pos'][0]]) {
            case '|':
                if ($this->axis[$beam['dir']] === 'X') $beam = $this->split($grid, $beam, 'Y'); break;
            case '-':
                if ($this->axis[$beam['dir']] === 'Y') $beam = $this->split($grid, $beam, 'X'); break;
            case "/":
                $beam['dir'] = $this->fs_map[$beam['dir']]; break;
            case "\\":
                $beam['dir'] = $this->bs_map[$beam['dir']]; break;
            case 'X':
                return 'done';
        }
        return $beam;
    }

    private function split($grid, $beam, $axis) {
        // Rule of thumb = when splitting always let the original beam rotate counter clockwise
        $beam['axis'] = $axis; // Designate the new beam axis
        $n_beam = $beam; // Clone the current beam

        // Rotate both beams. Old one counter clockwise, new one clockwise
        $beam['dir'] = $this->cc_map[$beam['dir']];
        $n_beam['dir'] = $this->c_map[$n_beam['dir']];

        $this->stack[] = $n_beam; // Add the new beam to the stack

        return $beam;
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
            return 'X'.trim($row).'X';
        }, $data);
        array_unshift($data, str_repeat('X', strlen($data[0])));
        array_push($data, str_repeat('X', strlen($data[0])));

        return $data;
    }
}