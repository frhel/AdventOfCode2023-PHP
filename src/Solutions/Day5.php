<?php
// ----------------------------------------------------------------------------
// Problem description: https://adventofcode.com/2023/day/5
// Solution by: https://github.com/frhel (Fry)
// Part 1: 282277027
// Part 2: 11554135
// ----------------------------------------------------------------------------
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use frhel\adventofcode2023php\Tools\Timer;
use frhel\adventofcode2023php\Tools\Prenta;
use frhel\adventofcode2023php\Tools\Utils;


class Day5 extends Day
{

    function __construct(private int $day, $bench = 0, $ex = 0) {
        parent::__construct($day, $bench, $ex);
    }

    public function solve($data) {
        $part1 = 0;
        $part2 = 0;

        $maps = $data['maps'];
        $seeds = $data['seeds'];

        // Part 1        
        $seeds = array_merge($seeds);
        foreach ($maps as $map) {
            $new_seeds = [];
            foreach ($seeds as $s) {
                $isset = false;
                foreach ($map as $mp) {
                    if ($s >= $mp['start'] && $s <= $mp['end']) {
                        $isset = true;
                        $new_seeds[] = $s + $mp['diff'];
                    }
                }
                if (!$isset) $new_seeds[] = $s;
            }
            $seeds = $new_seeds;
        }
        $part1 = min($seeds);
                
        // Part 2
        $pairs = $this->generate_pairs($data['seeds']);
        foreach ($maps as $map) {
            $new_pairs = [];
            while(count($pairs) > 0) {
                $p = array_pop($pairs);
                $last_count = count($new_pairs);
                foreach ($map as $mp) {
                    $l = 2; $u = 2;
                    if ($p[0] <= $mp['end']) $l = $p[0] < $mp['start'] ? 0 : 1;
                    if ($p[1] >= $mp['start']) $u = $p[1] > $mp['end'] ? 1 : 0;
                    switch ((int) ($l . $u)) {
                        case 10:
                            $new_pairs[] = [$p[0] + $mp['diff'], $p[1] + $mp['diff']];
                            break;
                        case 0:
                            $new_pairs[] = [$mp['start'] + $mp['diff'], $p[1] + $mp['diff']];
                            $pairs[] = [$p[0], $mp['start'] - 1];
                            break;
                        case 11:
                            $new_pairs[] = [$p[0] + $mp['diff'], $mp['end'] + $mp['diff']];
                            $pairs[] = [$mp['end'] + 1, $p[1]];
                            break;
                        case 1:
                            $new_pairs[] = [$mp['start'] + $mp['diff'], $mp['end'] + $mp['diff']];
                            $pairs[] = [$p[0], $mp['start'] - 1];
                            $pairs[] = [$mp['end'] + 1, $p[1]];
                            break;
                    }
                }
                if ($last_count === count($new_pairs)) {
                    $new_pairs[] = $p;
                }
            }
            $pairs = $new_pairs;
        }
        $part2 = min($pairs)[0];

        //return ['Redacted', 'Redacted'];
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

            $processed['start'] = $map[1];
            $processed['end'] = (int) $map[1] + $map[2] - 1;
            $processed['diff'] = $map[0] - $map[1];
            
            $current_map[] = $processed;
        }
        $maps[] = $current_map;

        return ['seeds' => $seeds, 'maps' => $maps];
    }

}
