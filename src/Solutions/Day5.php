<?php
// ----------------------------------------------------------------------------
// Problem description: https://adventofcode.com/2023/day/5
// Solution by: https://github.com/frhel (Fry)
// ----------------------------------------------------------------------------
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use frhel\adventofcode2023php\Tools\Timer;
use frhel\adventofcode2023php\Tools\Prenta;


class Day5
{

    function __construct(private int $day) {
        $ex = 0;

        // The test data is so small we may as well just load both files in anyways
        $data_full = file_get_contents(__DIR__ . '/../../data/day_' . $day);
        $data_example = file_get_contents(__DIR__ . '/../../data/day_' . $day . '.ex');

        // Default to example data. Just comment out this line to use the real data.
        // $ex = 1;
        $data = $this->parse_input($ex === 1 ? $data_example : $data_full);

        $overallTimer = new Timer();

        // Solve both parts at the same time. See solve() docblock for more info
        $solution = $this->solve($data);

        // Print answers
        Prenta::answer($solution[0], 1); // Part 1: 282277027
        Prenta::answer($solution[1], 2); // Part 2: 11554135
        Prenta::time($overallTimer->stop(), 'Overall Time');
    }


    protected function solve($data) { 
        $part1 = 0;
        $part2 = 0;

        $maps = $data['maps'];        

        // Part 1        
        $seeds = array_merge($data['seeds']);
        foreach ($maps as $map) {
            $new_seeds = [];
            foreach ($seeds as $s) {
                $isset = false;
                foreach ($map as $mp) {
                    if ($s >= $mp['src']['start'] && $s <= $mp['src']['end']) {
                        $isset = true;
                        $new_seeds[] = $s + $mp['diff'];
                    }
                }
                if (!$isset) {
                    $new_seeds[] = $s;
                }
            }
            $seeds = $new_seeds;
        }
        $part1 = min($seeds);
                
        // Part 2
        $pairs = $this->generate_pairs($data['seeds']);
        [$diff, $se, $ss] = [0, 0, 0];
        foreach ($maps as $map) {
            $new_pairs = [];
            while(count($pairs) > 0) {
                $p = array_pop($pairs);
                $last_count = count($new_pairs);
                foreach ($map as $mp) {
                    [$ss, $se, $diff] = [$mp['src']['start'], $mp['src']['end'], $mp['diff']];
                    if ($p[0] >= $ss && $p[1] <= $se) {
                        $new_pairs[] = [$p[0] + $diff, $p[1] + $diff];
                    } else if ($p[0] <= $ss && $p[1] <= $se && $p[1] >= $ss) {
                        $new_pairs[] = [$ss + $diff, $p[1] + $diff];
                        $pairs[] = [$p[0], $ss - 1];
                    } else if ($p[0] >= $ss && $p[1] >= $se && $p[0] <= $se) {
                        $new_pairs[] = [$p[0] + $diff, $se + $diff];
                        $pairs[] = [$se + 1, $p[1]];
                    } else if ($p[0] <= $ss && $p[1] >= $se) {
                        $new_pairs[] = [$ss + $diff, $se + $diff];
                        $pairs[] = [$p[0], $ss - 1];
                        $pairs[] = [$se + 1, $p[1]];
                    }
                }
                if ($last_count === count($new_pairs)) {
                    $new_pairs[] = $p;
                }
            }
            $pairs = $new_pairs;
        }
        $part2 = min($pairs)[0];

        return [$part1, $part2];
    }

    protected function generate_pairs($seeds) {
        $ranges = [];
        for ($i = 0; $i < count($seeds); $i++) {
            $ranges[] = [$seeds[$i], $seeds[$i] + $seeds[$i + 1] -1];
            $i++;            
        }
        
        return $ranges;
    }    

    protected function parse_input($data) {        
        $data = preg_split('/\r\n|\r|\n/', $data);

        $seeds = explode(' ', explode(': ', $data[0])[1]);
        $maps = [];

        $current_map = [];
        foreach ($data as $key => $line) {
            if ($key === 0) continue;
            $line = trim($line);
            if ($line === '') {
                unset($data[$key]);
                continue;
            }

            if (strpos($line, ':')) {
                if (count($current_map) > 0) {
                    $maps[] = $current_map;
                }
                $current_map = [];
                continue;
            }
            $map = explode(' ', $line);

            $processed['src'] = ['start' => $map[1], 'end' => (int) $map[1] + $map[2] - 1];
            $processed['diff'] = $map[0] - $processed['src']['start'];
            
            $current_map[] = $processed;
        }
        $maps[] = $current_map;

        return ['seeds' => $seeds, 'maps' => $maps];
    }

}
