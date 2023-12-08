<?php
// ----------------------------------------------------------------------------
// Problem description: https://adventofcode.com/2023/day/5
// Solution by: https://github.com/frhel (Fry)
// ----------------------------------------------------------------------------
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use Amp\Future;
use Amp\Parallel\Worker;

use frhel\adventofcode2023php\Tools\Timer;
use frhel\adventofcode2023php\Tools\RunTask;
use frhel\adventofcode2023php\Tools\Prenta;


class Day5
{

    function __construct(private int $day) {
        $prenta = new Prenta();
        $ex = 0;

        // The test data is so small we may as well just load both files in anyways
        $data_full = file_get_contents(__DIR__ . '/../../data/day_' . $day);
        $data_example = file_get_contents(__DIR__ . '/../../data/day_' . $day . '.ex');

        // $ex = 1;
        $data = $this->parse_input($ex === 1 ? $data_example : $data_full);

        // ====================================================================== //
        // ============================ Start Solving =========================== //
        // ====================================================================== //
        // Default to example data. Just comment out this line to use the real data.
        $this->ex = 1;
        $data = $this->parse_input($this->ex === 1 ? $data_example : $data_full);

        // Start the timer
        $overallTimer = new Timer();

        // Solve both parts at the same time. See solve() docblock for more info
        $solution = $this->solve($data);

        // Right answer: 282277027
        $prenta->answer($solution[0], 1);

        // Right answer: 11554135
        $prenta->answer($solution[1], 2);
 
        // Stop the timer
        $time_done = $overallTimer->stop();
        $prenta->time($time_done, 'Overall Time');
    }


    protected function solve($data) { 
        $part1 = 0;
        $part2 = 0;

        $seeds = $data['seeds'];
        $maps = $data['maps'];

        $pairs = array_map(fn($x) => [(int)$x, (int) $x], $seeds);
        print_r($pairs);

        foreach ($maps as $map) {
            $new_pairs = [];
            foreach ($map as $pair) {
                
            }
        }
        
        $pairs = $this->generate_pairs($seeds);
        print_r($pairs);

        print_r($maps);
        return [$part1, $part2];
    }

    protected function generate_pairs($seeds) {
        $ranges = [];
        for ($i = 0; $i < count($seeds); $i++) {
            $ranges[] = [$seeds[$i], $seeds[$i] + $seeds[$i + 1]];
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
            $current_map[] = explode(' ', $line);

            $processed['dest'] = $current_map[0][0] + $current_map[0][2];
            $processed['src'] = $current_map[0][0];
        }
        $maps[] = $processed;

        return ['seeds' => $seeds, 'maps' => $maps];
    }

}
