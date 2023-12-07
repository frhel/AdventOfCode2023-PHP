<?php
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use frhel\adventofcode2023php\Tools\Timer;

class Day extends Command
{
    protected static $day;
    protected static $defaultName;
    protected static $defaultDescription = 'Advent of Code 2023 Solution';
    protected static $bashColors = ['blue' => '\033[34m', 'light_blue' => '\033[94m', 'green' => '\033[32m', 'light_green' => '\033[92m', 'cyan' => '\033[36m', 'light_cyan' => '\033[96m', 'red' => '\033[31m', 'light_red' => '\033[91m', 'purple' => '\033[35m', 'light_purple' => '\033[95m', 'brown' => '\033[33m', 'yellow' => '\033[93m', 'light_gray' => '\033[37m', 'white' => '\033[97m', 'normal' => '\033[0m', 'bold_white' => '\033[1m', 'bold_light_cyan' => '\033[1;96m', 'bold_light_green' => '\033[1;92m'];
    protected $dataFile;
    protected $exampleFile;
    protected $ex;

    function __construct() {
        $this->ex = 0;

        $this->dataFile = __DIR__ . '/../../data/day_' . self::$day;
        $this->exampleFile = __DIR__ . '/../../data/day_' . self::$day . '.ex';

        parent::__construct();
    }

    // ----------------------------------------------------------------------------
    // Problem description:
    // Solution by: https://github.com/frhel (Fry)
    // ----------------------------------------------------------------------------
    protected function execute(InputInterface $input, OutputInterface $output) {
        $colors = self::$bashColors;
        $io = new SymfonyStyle($input, $output);
        $io->writeln(self::$defaultName . ' - ' . self::$defaultDescription);

        // The test data is so small we may as well just load both files in anyways
        $data_full = file_get_contents($this->dataFile);
        $data_example = file_get_contents($this->exampleFile);

        // Default to example data. Just comment out this line to use the real data.
        $this->ex = 1;
        $data = $this->parse_input($this->ex === 1 ? $data_example : $data_full);

        // ====================================================================== //
        // ============================ Start Solving =========================== //
        // ====================================================================== //
        // Start the timer
        $overallTimer = new Timer();

        $solution = $this->solve($data);

        // Right answer: 
        $this->print_answer($solution[0], 1);

        // Right answer: 
        $this->print_answer($solution[1], 2);
        
        // Stop the timer
        $this->print_time($overallTimer->stop(), 'Overall Time');

        return Command::SUCCESS;
    }

    /**
     * Solves the problem
     * 
     * @param array $data The data to solve
     * @return array The solution to the problem in the form of [part1, part2]
     */
    protected function solve($data) {
        $solution = 0;
        


        return [$solution, 0];
    }

    protected function parse_input($data) {        
        $data = preg_split('/\r\n|\r|\n/', $data);



        return $data;
    }






    // ----------------------------------------------------------------------------
    // Helper functions -----------------------------------------------------------
    // ----------------------------------------------------------------------------
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
    protected function print_answer($solution, $part) {
        $colors = self::$bashColors;
        printf('%sPart %s Solution: %s%s%s' . PHP_EOL, $colors['bold_light_cyan'], $part, $colors['bold_light_green'], $solution, $colors['normal']);
    }

    /**
     * Prints the time taken to run a function
     *
     * @param float $time Time taken to run function
     * @param string $message Message to print e.g. 'Time to run function'
     * @return void
     * 
     * @example 1 print_time($time, 'Time to run function');
     * 
     */
    protected function print_time($time, $message) {
        $colors = self::$bashColors;
        printf('%s%s: %s%s%s' . PHP_EOL, $colors['bold_light_cyan'], $message, $colors['bold_light_green'], $time, $colors['normal']);
    }

}