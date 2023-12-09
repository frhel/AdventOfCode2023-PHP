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

class Day9
{
    function __construct(private int $day, $bench = false, $ex = 0) {
        // $ex = 1;
        $data = $this->load_data($day, $ex);        
        if ($bench) return; // Don't run the actual solution if we're benchmarking

        $overallTimer = new Timer();
        // Solve both parts at the same time. See solve() docblock for more info
        $solution = $this->solve($data);        
        Prenta::time($overallTimer->stop(), 'First run time');
        Prenta::answer($solution[0], 1);
        Prenta::answer($solution[1], 2);

        Utils::bench(9, $data, 10000); // 0 runs to turn off
    }
    

    /**
     * Solves the problem
     * 
     * @param array $data The data to solve
     * @return array The solution to the problem in the form of [part1, part2]
     */
    static function solve($data) {
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

    protected function parse_input($data) {        
        $data = preg_split('/\r\n|\r|\n/', $data);
        foreach ($data as $key => $line) {
            preg_match_all('/-*[0-9]\w*/', $line, $line);
            $data[$key] = $line[0];
        }

        return $data;
    }

    public function load_data($day, $ex = 0) {
        // The test data is so small we may as well just load both files in anyways
        $data_full = file_get_contents(__DIR__ . '/../../data/day_' . $day);
        $data_example = file_get_contents(__DIR__ . '/../../data/day_' . $day . '.ex');

        return $this->parse_input($ex === 1 ? $data_example : $data_full);
    }
}