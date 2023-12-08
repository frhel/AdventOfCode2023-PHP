<?php

namespace frhel\adventofcode2023php\Tools;

class Prenta {

    protected static $bashColors = [
        'light_blue' => "\033[1;34m",
        'light_green' => "\033[1;32m",
        'light_cyan' => "\033[1;36m",
        'light_red' => "\033[1;31m",
        'light_purple' => "\033[1;35m",
        'light_yellow' => "\033[1;33m",
        'normal' => "\033[0m"
    ];

    function __construct() {
    }

    /**
     * Prints a message to the console
     *
     * @param string $message Message to print
     * @param string $color Colour to print the message in
     * @return void
     * 
     * @example 1 print('Hello World', 'light_blue');
     * 
     */
    static public function print($message, $color = 'normal') {
        $c = self::$bashColors;
        printf('%s %s %s' . PHP_EOL, $c[$color], $message, $c['normal']);
    }

    /**
     * Prints a message to the console if the frequency is 0 or the count is a multiple of the frequency
     *
     * @param int $count Current count
     * @param int $frequency Frequency to print at
     * @param string $label Label to print
     * @param string $value Value to print
     * @return void
     * 
     * @example 1 freq_interval($count, $frequency, 'Current count', $count);
     * 
     */
    static public function freq_interval($count, $frequency , $label, $value) {
        $c = self::$bashColors;
        if ($frequency === 0 || $count % $frequency === 0) 
            printf('%s %s: %s %s %s' . PHP_EOL, $c['light_cyan'], $label, $c['light_yellow'], $value, $c['normal']);
    }

    /**
     * Prints the answer to the console
     *
     * @param int $solution Solution to the problem
     * @param int $part Which part of the problem is being solved
     * @return void
     * 
     * @example 1 print_answer($solution[0], 1);
     * 
     */
    static public function answer($solution, $part) {
        $c = self::$bashColors;
        // Echo the colours so the escape characters go too
        printf('%s Part %s: %s %s %s' . PHP_EOL, $c['light_purple'], $part, $c['light_cyan'], $solution, $c['normal']);
    }

    /**
     * Prints the time taken to run a function
     *
     * @param string $time Time taken to run function
     * @param string $message Message to print e.g. 'Time to run function'
     * @return void
     * 
     * 
     * @example 1 print_time($time, 'Time to run function');
     * 
     */
    static public function time($time, $message) {
        $c = self::$bashColors;
        printf('%s %s: %s %s %s' . PHP_EOL, $c['light_cyan'], $message, $c['light_yellow'], $time, $c['normal']);
    }
}