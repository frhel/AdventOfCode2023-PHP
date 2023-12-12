<?php
// ----------------------------------------------------------------------------
// Problem description: https://adventofcode.com/2023/day/10
// Solution by: https://github.com/frhel (Fry)
// Part 1: 6831
// Part 2: 305
// ----------------------------------------------------------------------------
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use frhel\adventofcode2023php\Tools\Timer;
use frhel\adventofcode2023php\Tools\Prenta;
use frhel\adventofcode2023php\Tools\Utils;
use SplStack;

class Day10 extends Day 
{
    private $components = [];
    private $dirs = [];
    private $offsets = [];
    private $rotas = [];
    function __construct(private int $day, $bench = 200, $ex = 0) {
        // $ex = 1;

        $this->components = [
            // The values say which directions we GO TO
            // Not which directions we COME FROM
            // So if we come from the left, we can go right, and vice versa
            '|' => ['D' => [0, 1, 'x'], 'U' => [0, -1, 'x']],
            '-' => ['R' => [1, 0, 'x'], 'L' => [-1, 0, 'x']],
            'L' => ['D' => [1, 0, 'L'], 'L' => [0, -1, 'R']],
            'J' => ['D' => [-1, 0, 'R'], 'R' => [0, -1, 'L']],
            '7' => ['U' => [-1, 0, 'L'], 'R' => [0, 1, 'R']],
            'F' => ['U' => [1, 0, 'R'], 'L' => [0, 1, 'L']]
        ];
        $this->dirs = ['1,0' => 'R', '-1,0' => 'L', '0,1' => 'D', '0,-1' => 'U'];

        $this->offsets = [
            [0, 1], [1, 0], [0, -1], [-1, 0]
        ];

        $this->rotas = [
            'R' => [
                'D' => 'L',
                'L' => 'U',
                'U' => 'R',
                'R' => 'D'
            ],
            'L' => [
                'D' => 'R',
                'L' => 'D',
                'U' => 'L',
                'R' => 'U'
            ]
        ];

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

        [$map, $start] = [$data[0], $data[1]];
        $start = $this->find_start($map, $start);
        $start = ['x' => $start[0], 'y' => $start[1], 'rota' => $start[2]];
        
        $loop = $this->walk_path($map, $start);

        $part1 = count($loop) / 2;       
        $part2 = $this->calc_inner_area($map, $loop);

        return [$part1, $part2];
    }

    /**
     * Walks the path from $start and returns the path as an array of coordinates
     * 
     * @param array $map The map
     * @param array $start The starting point
     * @return array The path
     */
    protected function walk_path($map, $start) {
        $rota = $start['rota'];
        $stack = new SplStack();
        $stack->push($start);
        // Pushing to the path with the key as stringified coords
        // so I can easily check if a coord is already in the path
        // in part 2
        $path[$start['x'] . ',' . $start['y']] = $start;

        $count = 0;
        while (!$stack->isEmpty()) {
            $count++;
            $curr = $stack->pop();
            [$x, $y] = [$curr['x'], $curr['y']];            
            $c = $map[$y][$x]; // current pipe component
            $c_rules = $this->components[$c]; // current pipe component rules
            [$nx, $ny, $turn_dir] = $c_rules[$rota]; // next x, next y, next turn direction

            if ($turn_dir !== 'x' && $this->rotas[$turn_dir][$rota] !== 'x') {
                $rota = $this->rotas[$turn_dir][$rota];
            }           

            $next = ['x' => $x + $nx, 'y' => $y + $ny]; // next grid position

            if ($map[$next['y']][$next['x']] === 'S') {
                // We've reached the start again, exit
                $path[$next['x'] . ',' . $next['y']] = $next;
                break;
            }

            $stack->push(['x' => $next['x'], 'y' => $next['y']]);
            $path[$next['x'] . ',' . $next['y']] = $next;
        }

        return $path;
    }

    /**
     * Calculates the inner area of the path, assuming the path is a loop
     * 
     * @param array $map The map
     * @param array $path The path
     * @return int The inner area
     */
    protected function calc_inner_area($map, $path) {
        $acc = 0;
        // iterate over the map line by line
        foreach ($map as $y => $line) {
            foreach($line as $x => $glyph) {
                // reset the counter when we hit the start of a new line
                if ($x == 0) $count = 0;

                if (!isset($path[$x.','.$y])) {
                    // If we're not in the path and the count is on, add to the accumulator
                    // it is a weird way to use modulo against true/false but it works I guess
                    if ($count % 2) $acc++;
                } else {
                    // First wall turns the count on, second turns it off
                    if ($glyph == "|") $count += 1;
                    // If we hit a corner, switch off the count temporarily
                    // and reactivate it when the opposite corner is hit
                    if ($glyph == "J" || $glyph == "F") $count += .5;
                    if ($glyph == "L" || $glyph == "7") $count -= .5;
                }
            }
        }

        return $acc;
    }

    /**
     * Finds the starting point in the map
     * 
     * @param array $map The map
     * @param array $start The starting point
     * @return array The coordinates of the character
     */
    protected function find_start($map, $start) {        
        // check all directions for a valid path
        [$x, $y, $dir] = [$start[0], $start[1], ''];
        foreach ($this->offsets as $offset) {
            [$x, $y] = [$start[0] + $offset[0], $start[1] + $offset[1]];
            $glyph = $map[$y][$x];
            $dir = $this->dirs[$offset[0].','.$offset[1]];
            if        ($dir === 'R' && ($glyph === 'J' || $glyph === '-' || $glyph === '7')) {
                break;
            } else if ($dir === 'L' && ($glyph === 'L' || $glyph === '-' || $glyph === 'F')) {
                break;
            } else if ($dir === 'U' && ($glyph === '7' || $glyph === '|' || $glyph === 'F')) {
                break;
            } else if ($dir === 'D' && ($glyph === 'J' || $glyph === '|' || $glyph === 'L')) {
                break;
            }

        }
        return [$x, $y, $dir];
    }

    /**
     * Parses the input data into a usable format
     * 
     * @param string $data The input data
     * @return array The parsed data
     */
    protected function parse_input($data) {        
        $data = preg_split('/\r\n|\r|\n/', $data);

        $start = [0,0];
        $map = [];
        foreach ($data as $y => $line) {
            // print which type line is array or string
            if (strpos($line, 'S') !== false)
                $start = [strpos($line, 'S'), $y];
            $map[] = str_split($line);
        }

        return [$map, $start];
    }
}