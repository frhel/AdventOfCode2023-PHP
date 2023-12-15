<?php

namespace frhel\adventofcode2023php\Tools;

use frhel\adventofcode2023php\Tools\Timer;
use frhel\adventofcode2023php\Tools\Prenta;

class Utils {
    
    static public function bench($day, $data, $times) {
        if ($times === 0) return;

        Prenta::std(        'Running benchmark       ' . $times . ' rounds', 'white', 'purple');
        echo PHP_EOL;

        $day_path = '\\frhel\\adventofcode2023php\\Solutions\\Day' . $day;
        $day = new $day_path($day, -1); // true to stop after loading data
        $memory = [];
        $timer = new Timer();
        $timer->start();
        for ($i = 1; $i <= $times; $i++) {            
            memory_reset_peak_usage();
            $day->solve($data);
            $timer->checkpoint();
            $memory[] = memory_get_peak_usage();
        }
        $avg_mem = round((array_sum($memory) / count($memory))/pow(2, 20), 2);
        $peak_mem = round(max($memory)/pow(2, 20), 2);
        $left_pad = str_repeat(' ', strlen((string)$times));
        
        Prenta::label('          Total time', '' . $timer->stop(), 'cyan');
        Prenta::label('        Average time', '' . $timer->avg_time(), 'cyan');
        Prenta::label('         Median time', '' . $timer->median_time(), 'cyan');
        Prenta::label('Average memory usage', $avg_mem . ' MiB', 'green');
        Prenta::label('         Peak Memory', $peak_mem . " MiB", 'green');
        echo PHP_EOL;
        Prenta::std($left_pad."                               ", 'normal', 'purple');
        echo PHP_EOL;
    }
    /**
     * Calculates the Least Common Multiple of all the numbers in the array
     * @depends gcd
     * 
     * @param array $arr The array of numbers
     * @return int The Least Common Multiple of all the numbers in the array
     */
    public function lcm($arr) {
        // Reduce the array by multiplying all the numbers together and dividing by the Greatest Common Divisor
        // of the current number and the accumulator
        return array_reduce($arr, fn($carry, $item) => ($carry * $item) / $this->gcd($carry, $item), 1);
    }

    /**
     * Calculates the Greatest Common Divisor of two numbers
     * 
     * @param int $a First number
     * @param int $b Second number
     * @return int The Greatest Common Divisor of the two numbers
     */
    public function gcd($a, $b) {
        // Make sure $a is the smaller number
        [$a, $b] = $a > $b ? [$b, $a] : [$a, $b];
        
        // Literally just loop over all the numbers from 2 to $b and check if they divide into both $a and $b
        for ($gcd = 2; $gcd <= $b; $gcd++) {
            if ($a % $gcd === 0 && $b % $gcd === 0) return $gcd;
        }
        return 1;
    }
}