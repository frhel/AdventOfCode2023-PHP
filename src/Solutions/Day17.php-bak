<?php
// ----------------------------------------------------------------------------
// Problem description: https://adventofcode.com/2023/day/17
// Solution by: https://github.com/frhel (Fry)
// Part1:
// Part2:
// ----------------------------------------------------------------------------
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use frhel\adventofcode2023php\Tools\MinPriorityQueue;
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
        $this->target = implode(',',[array_key_last($grid[0]), array_key_last($grid)]);
        $winner = null;
        // print_r($grid);
        $pq = new MinPriorityQueue();
        $max = count($grid[0]) * 9;
        $start = &$grid[0][0];
        $start['total'] = $start['val'];
        $start['weight'] = $start['total'];
        $start['vc'] = 1;
        $pq->insert($start, $start['total']);
        $count = 0;
        $max_q_size = 0;
        while(!$pq->isEmpty()) {
            if ($count % 100000 == 0) {
                echo 'Queue size: ' . $pq->count() . PHP_EOL;
            }
            $count++;
            if ($pq->count() > $max_q_size) $max_q_size = $pq->count();

            $a = $pq->extract();

            if ($a['strpos'] == $this->target) {
                $winner = $a;
                echo 'Winner!' . PHP_EOL;
                print_r($a);
                break;
            }
            $dirs = [&$a['dir'], &$this->cc_map[$a['dir']], &$this->c_map[$a['dir']]];
            foreach ($dirs as &$dir) {
                $d = &$this->dir[$dir];
                // echo 'Dir: ' . $dir . PHP_EOL;
                // echo 'D ' . $d[0] . ',' . $d[1] . PHP_EOL;
                if ($a['dir'] == $dir) {
                    if ($a['vc'] === 3) continue;
                    $vc = $a['vc'] + 1;
                } else {
                    $vc = 1;
                }

                $n_pos = [$a['pos'][0] + $d[0], $a['pos'][1] + $d[1]];
                if (!$this->in_bounds($grid, $n_pos)) continue;
                $n = &$grid[$n_pos[1]][$n_pos[0]];

                $total = $a['total'] + $n['val'];
                if ($n['total'] > 0 && $total > $n['total']) continue;
                $weight = $total;//$n['weight'] + $n['total'] + abs($n['val'] / $a['val']) + $this->calc_dist($n_pos, $this->target);


                // if ($n['weight'] > 0 && $n['weight'] <= $weight) continue;
                $n['total'] = $total;
                $n['weight'] = $total;
                $n['vc'] = $vc;
                $n['prev'] = $a['strpos'];
                $n['dir'] = $dir;


                $pq->insert($n, $n['total']);
            }
        }



        echo 'Queue size: ' . count($pq) . PHP_EOL;
        // echo 'Visited size: ' . count($visited) . PHP_EOL;
        echo 'Max queue size: ' . $max_q_size . PHP_EOL;

    //     // print_r($visited);
    //     // print_r($winner);
    //     // print_r($visited);

    //     // $backtracked = $this->backtrac($winner, $visited);
    //     // print_r($backtracked);

        $part1 = $winner['total'];
        $btracked = $this->backtrack($winner, $grid);

        return [$part1, $part2];
    }

    protected function backtrack($winner, $grid) {
        $path = [];
        $node = $winner;
        $total = 0;
        $pos = $node['strpos'];
        $prev = explode(',',$node['prev']) ?? [0,0];
        // print_r($node);
        while ($pos != '') {
            $pos = $node['strpos'];
            $path[$pos] = $node['strpos'];
            $total += $node['val'];
            // print_r($node);
            echo 'pos: ' . $pos . 'val: ' . $node['val'] . ' total: ' . $total . PHP_EOL;

            if ($node['prev'] == '') break;
            $prev = explode(',',$node['prev']);
            $node = $grid[$prev[1]][$prev[0]];
            // print_r($node);
        }
        $this->print_map($grid, $path);
        return $path;
    }

    protected function print_map($grid, $path) {
        $map = [];
        foreach ($grid as $row) {
            $r = [];
            foreach ($row as $col) {
                if (array_key_exists($col['strpos'], $path)) {
                    $r[] = '#';
                } else {
                    $r[] = $col['val'];
                }
            }
            echo implode(' ', $r) . PHP_EOL;
        }

        print_r($grid[2][0]);
    }

    //     $node = [
    //         'pos' => [0,0],
    //         'strpos' => '0,0',
    //         'total' => $grid[0][0],
    //         'prev' => [0,0],
    //         'steps' => 1,
    //         'weight' => 1,
    //         'dir' => 'E',
    //         'vc' => 1,
    //     ];

    //     $pq = [];
    //     $pq['a'] = 1;
    //     $pq['b'] = 50;
    //     $pq['c'] = 10;
    //     $pq['d'] = 100;
    //     $pq['e'] = 5;
    //     $pq['f'] = 20;

    //     usort($pq, function($a, $b) {
    //         return $a <=> $b;
    //     });

    //     print_r($pq);

    //     $winner = null;
    //     // $queue = new MinPriorityQueue();

    //     // $queue->setExtractFlags(MinPriorityQueue::EXTR_BOTH);
    //     // $queue->insert($node, $node['total']);
    //     $pq = [];
    //     $visited = [implode(',', $node['pos']) => $node];
    //     $pq[implode(',', $node['pos'])] = $node;
    //     echo $this->target[0] . ',' . $this->target[1] . PHP_EOL;
    //     echo 'Last nr: ' . $grid[$this->target[1]][$this->target[0]] . PHP_EOL;
    //     $max_q_size = 0;
    //     $count = 0;
    //     while (count($pq) > 0) {
    //         usort($pq, function($a, $b) {
    //             return $b['weight'] <=> $a['weight'];
    //         });
    //         if ($count % 1000 == 0) {
    //             echo 'Queue size: ' . count($pq) . PHP_EOL;
    //             echo 'Visited size: ' . count($visited) . PHP_EOL;
    //         }
    //         $count++;
    //         if (count($pq) > $max_q_size) $max_q_size = count($pq);

    //         $a = array_pop($pq);
    //         // print_r($a);

    //         $visited[implode(',', $a['pos'])] = $a;

    //         if (implode(',', $a['pos']) == implode(',', $this->target)) {
    //             $winner = $a;
    //             break;
    //         };

    //         foreach ($this->dir as $d) {
    //             if ($a['dir'] == $d) {
    //                 if ($a['vc'] == 3) continue;
    //                 $vc = $a['vc'] + 1;
    //             } else {
    //                 $vc = 1;
    //             }

    //             $n_pos = [$a['pos'][0] + $d[0], $a['pos'][1] + $d[1]];
    //             if (!$this->in_bounds($grid, $n_pos) || implode(',', $n_pos) === implode(',', $a['pos'])) continue;
    //             $n_total = $a['total'] + $grid[$n_pos[1]][$n_pos[0]];

    //             $n = $a;
    //             $n['total'] = $n_total;
    //             $n['pos'] = $n_pos;
    //             $n['strpos'] = implode(',', $n_pos);
    //             $n['prev'] = $a['pos'];
    //             $n['weight'] = $n['total'];// + $this->calc_dist($n_pos, $this->target) + abs($grid[$n_pos[1]][$n_pos[0]] - $grid[$a['pos'][1]][$a['pos'][0]]);// + $this->calc_dist($n_pos, $this->target);
    //             $n['steps'] = $a['steps'] + 1;
    //             $n['dir'] = $d;
    //             $n['vc'] = $vc;

    //             $v = $visited[$n['strpos']] ?? null;
    //             if ($v !== null) continue;
    //         //     // if it exists in the queue, check if the new weight is better
    //             if (array_key_exists($n['strpos'], $pq)) {

    //                 echo 'Queue: ' . $n['strpos'] . PHP_EOL;
    //                 $p = $pq[$n['strpos']];
    //                 $pweight = $p['weight'];
    //                 // continue;
    //                 if ($pweight >= $nweight)

    //                     $pq[$n['strpos']] = $n;
    //                     // echo 'Visited: ' . $key . ':' . $vweight . ' ' . implode(',', $a['pos']) . ':' . $nweight . PHP_EOL;
    //                     continue;
    //                 // }
    //             }

    //             $pq[$n['strpos']] = $n;



    //             // echo 'Visited: ' . implode(',', $n['pos']) . ':' . implode(',', $n['pos']) . ' ' . implode(',', $a['pos']) . PHP_EOL;


    //         }
    //         // if (count($pq) > 1000) {
    //         //     $pq = array_filter($pq, function($a) use($pq) {
    //         //         return $a['weight'] > end($pq)['weight'];
    //         //     });
    //         // }
    //     }

    //     echo 'Queue size: ' . count($pq) . PHP_EOL;
    //     echo 'Visited size: ' . count($visited) . PHP_EOL;
    //     echo 'Max queue size: ' . $max_q_size . PHP_EOL;

    //     // print_r($visited);
    //     // print_r($winner);
    //     // print_r($visited);

    //     // $backtracked = $this->backtrac($winner, $visited);
    //     // print_r($backtracked);

    //     $part1 = $winner['total'];


    //     return [$part1, $part2];
    // }



    protected function in_bounds($grid, $pos) {
        return $pos[0] >= 0 && $pos[1] >= 0 && $pos[0] < count($grid[0]) && $pos[1] < count($grid);
    }

    protected function calc_dist($start, $end) {
        return abs($start[0] - $end[0]) + abs($start[1] - $end[1]);
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

        $grid = [];
        foreach ($data as $key => $row) {
            $r = [];
            foreach ($row as $k => $v) {
                $r[] = [
                    'strpos' => $k.','.$key,
                    'val' => $v,
                    'dir' => 'E',
                    'weight' => 0,
                    'vc' => 0,
                    'prev' => '',
                    'total' => 0,
                    'visited' => false,
                    'pos' => [$k, $key]
                ];
            }
            $grid[] = $r;
        }

        return $grid;
    }
}