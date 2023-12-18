<?php
// ----------------------------------------------------------------------------
// Problem description: https://adventofcode.com/2023/day/17
// Solution by: https://github.com/frhel (Fry)
// Part1:
// Part2:
// ----------------------------------------------------------------------------
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use frhel\adventofcode2023php\Tools\Timer;
use frhel\adventofcode2023php\Tools\Prenta;
use frhel\adventofcode2023php\Tools\Utils;

class Day17 extends Day
{

    private $dir;
    private $cc_map;
    private $c_map;
    private $target;

    function __construct(private int $day, $bench = 0, $ex = 0) {

        $ex = 1;

           $this->dir = ['E' => [1,0], 'S' => [0,1], 'N' => [0,-1], 'W' => [-1,0]];
        $this->cc_map = ['N' => 'W', 'W' => 'S', 'S' => 'E', 'E' => 'N'];
         $this->c_map = ['N' => 'E', 'E' => 'S', 'S' => 'W', 'W' => 'N'];

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

        $pq = [];
        $this->target = (count($grid[0])-1) . ',' . (count($grid)-1);
        $start = ['0,0' => ['pos' => [0,0], 'total' => $grid[0][0], 'dir' => 'E', 'dist' => 0, 'path' => ['0,0']]];
        $visited = [$start[0]['pos'][0] . ',' . $start[0]['pos'][1] => $start['total']];
        $pq = array_merge($pq, $start);
        while (count($pq) > 0) {

            usort($pq, function($a, $b) {
                return $a['total'] <=> $b['total'];
            });
            $node = array_shift($pq);

            $dirs = [&$node['dir'], &$this->cc_map[$node['dir']], &$this->c_map[$node['dir']]];
            foreach ($dirs as &$dir) {

                $d = &$this->dir[$dir];
                if ($node['dir'] == $dir) {
                    if ($node['dist'] === 3) continue;
                    $dist = $node['dist'] + 1;
                } else {
                    $dist = 1;
                }

                // echo 'Pos: ' . $node['pos'][0] . ',' . $node['pos'][1] . ' Dir: ' . $dir . ' Dist: ' . $dist . PHP_EOL;
                // echo 'D ' . $d[0] . ',' . $d[1] . PHP_EOL;

                $next_coords = [$node['pos'][0] + $d[0], $node['pos'][1] + $d[1]];
                if (!$this->in_bounds($grid, $next_coords)) continue;
                $next_key = $next_coords[0] . ',' . $next_coords[1];
                $next_val = $grid[$next_coords[1]][$next_coords[0]];

                echo 'Next: ' . $next_coords[0] . ',' . $next_coords[1] . ' Val: ' . $next_val . PHP_EOL;
                echo 'Target: ' . $this->target . PHP_EOL;
                if ($next_key === $this->target) {

                    $part1 = $node['total'] + $next_val;
                    break 2;
                }

                $next = [
                    'pos' => $next_coords,
                    'total' => $node['total'] + $next_val,
                    'dir' => $dir,
                    'dist' => $dist,
                    'path' => array_merge($node['path'], [$next_key])
                ];

                if (isset($visited[$next_key]) && $visited[$next_key] <= $next['total']) continue;
                echo isset($pq[$next_key]) ? 'Exists' : 'New' . PHP_EOL;
                if (isset($pq[$next_key])) {
                    if ($pq[$next_key]['total'] >= $next['total']) {
                        $pq[$next_key] = $next;
                        continue;
                    } else {
                        continue;
                    }
                } else {
                    $visited[$next_key] = $next['total'];
                    $pq[$next_key] = $next;
                }

            }
            // echo 'PQ: ' . count($pq) . PHP_EOL;
        }



        return [$part1, $part2];
    }

    protected function in_bounds($grid, $pos) {
        return $pos[0] >= 0 && $pos[1] >= 0 && $pos[0] < count($grid[0]) && $pos[1] < count($grid);
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
            return str_split(trim($row));
        }, $data);

        return $data;
    }
}