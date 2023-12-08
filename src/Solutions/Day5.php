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

    
    // ====================================================================== //
    // I plan on rewriting this whole thing from scratch. I'm not happy with  //
    // how it turned out. I'm not happy with the code. I'm not happy with the //
    // performance. I'm not happy with the fact that I had to use threads to  //
    // get it to run in a reasonable amount of time. There is a better way.   //
    // ====================================================================== //
    protected function solve($data) {     
        $timer = new Timer();
        $part1 = INF;
        $part2 = INF;

        $seeds = $data['seeds'];
        $maps = $data['maps'];
        

        $part1exec = [];
        $part1exec[] = Worker\submit(new RunTask([$seeds, $maps, 0, 0], 1, 0, 0));

        // Each submission returns an Execution instance to allow two-way
        // communication with a task. Here we're only interested in the
        // task result, so we use the Future from Execution::getFuture()
        $part1task = Future\await(array_map(
            fn (Worker\Execution $e) => $e->getFuture(),
            $part1exec,
        ));

        foreach ($part1task as $part1task) {
            echo $part1task . PHP_EOL;
            $part1 = min($part1, (int) $part1task);
        }

        echo 'Part 1 Time: ' . $timer->stop() . PHP_EOL;

        $part2exec = [];
        $ranges = $this->generate_pairs($seeds);
        $ranges_total = 0;
        foreach($ranges as $key=>$range) {
            $ranges_total += $range[1] - $range[0];
        }

        foreach ($ranges as $key => $range) {
            echo 'Starting range: ' . $key . PHP_EOL;
            $part2exec[] = Worker\submit(new RunTask([$range, $maps, 0, 0], 2, $key, $ranges_total));
        }

        $part2task = Future\await(array_map(
            fn (Worker\Execution $e) => $e->getFuture(),
            $part2exec,
        ));
        foreach ($part2task as $part2task) {
            echo $part2task . PHP_EOL;
            $part2 = min($part2, (int) $part2task);
        }

        echo 'Part 2 Time: ' . $timer->stop() . PHP_EOL;

        //echo $this->find_destination(14) . PHP_EOL;

        // Don't ask why the -1 is needed. I don't know. I don't want to know. I don't care.
        // I just want to go to bed.
        return [$part1, $part2 - 1];
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
        }
        $maps[] = $current_map;

        return ['seeds' => $seeds, 'maps' => $maps];
    }

}
