<?php
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use frhel\adventofcode2023php\Tools\Timer;
use frhel\adventofcode2023php\Tools\Prenta;
use frhel\adventofcode2023php\Tools\Utils;

class Day
{
    public $ex;
    public array $data;
    function __construct(private int $day, $bench = 0, $ex = 0) {
        // $ex = 1; // comment out to run the full data
        $this->ex = $ex;
        $this->day = $day;
        $data = $this->parse_input($this->load_data($day, $ex)); $this->data = $data;
        $this->data = $data;
        if ($bench < 0) return; // The benchmarking code will call solve() directly if activated

        $left_pad = str_repeat(' ', strlen((string)$day));
        echo PHP_EOL;
        //Prenta::std($left_pad."                              ", 'normal', 'red');
        Prenta::std("       Solving day nr. $day       ", 'white', 'cyan');
        echo PHP_EOL;

        $overallTimer = new Timer();
        // Solve both parts at the same time. See solve() docblock for more info
        $solution = $this->solve($data);


        Prenta::label('Execution time', $overallTimer->stop(),  'cyan');
        Prenta::answer($solution[0], 1);
        Prenta::answer($solution[1], 2);
        echo PHP_EOL;
        Prenta::std($left_pad."                              ", 'normal', 'cyan');
        echo PHP_EOL;

        Utils::bench($day, $data, $bench); // 0 runs to turn off
    }


    /**
     * Solves the problem. Needs to be public so we can call it from the benchmarking code
     *
     * @param array $data The data to solve
     * @return array The solution to the problem in the form of [part1, part2]
     */
    public function solve($data, $part1 = 0, $part2 = 0) {

        return [&$part1, &$part2];
    }

    protected function parse_input($data) {
        $data = preg_split('/\r\n|\r|\n/', $data);


        return $data;
    }

    public function load_data($day, $ex = 0) {
        // The test data is so small we may as well just load both files in anyways
        $data_full = file_get_contents(__DIR__ . '/../../data/day_' . $day);
        $data_example = file_get_contents(__DIR__ . '/../../data/day_' . $day . '.ex');
        return $ex === 1 ? $data_example : $data_full;
    }
}