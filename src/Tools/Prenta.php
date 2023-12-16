<?php

namespace frhel\adventofcode2023php\Tools;

class Prenta {

    static public $bashColors = [
        'blue' => "\033[1;34m",
        'green' => "\033[1;32m",
        'cyan' => "\033[1;36m",
        'red' => "\033[1;31m",
        'purple' => "\033[1;35m",
        'yellow' => "\033[1;33m",
        'white' => "\033[1;37m",
        'normal' => "\033[0m"
    ];

    static public $bgColors = [
        'blue' => "44",
        'green' => "42",
        'cyan' => "46",
        'red' => "41",
        'purple' => "45",
        'yellow' => "43",
        'white' => "47",
        'grey' => "100",
        'normal' => "49"
    ];

    function __construct() {
    }

    /**
     * Prints a message to the console
     * Available colours are: blue, green, cyan, red, purple, yellow, normal
     *
     * @param string $message Message to print
     * @param string $color Colour to print the message in
     * @return void
     * 
     * @example 1 print('Hello World', 'blue');
     * 
     */
    static public function std($message, $color = 'white', $bg = 'normal') {
        $c = self::$bashColors;
        $bgc = self::$bgColors;
        $color = $c[$color];
        if ($bg !== 'normal') $color = str_replace('m', ';' . $bgc[$bg] . 'm', $color);
        printf('%s %s %s' . PHP_EOL, $color, $message, $c['normal']);
    }

    /**
     * Prints a label and a value to the console
     * Available colours are: blue, green, cyan, red, purple, yellow, normal
     *
     * @param string $label Label to print
     * @param string $value Value to print
     * @param string $color Colour to print the label in
     * @return void
     * 
     * @example 1 label('Current count', $count, 'white');
     * 
     */
    static public function label($label, $value, $color = 'white') {
        $c = self::$bashColors;
        printf('%s %s: %s %s %s' . PHP_EOL, $c[$color], $label, $c['yellow'], $value, $c['normal']);
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
            printf('%s %s: %s %s %s' . PHP_EOL, $c['white'], $label, $c['yellow'], $value, $c['normal']);
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
        printf('%s Part %s: %s %s %s' . PHP_EOL, $c['white'], $part, $c['cyan'], $solution, $c['normal']);
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
        printf('%s %s: %s %s %s' . PHP_EOL, $c['purple'], $message, $c['yellow'], $time, $c['normal']);
    }
}