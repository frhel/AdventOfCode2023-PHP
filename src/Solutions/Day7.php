<?php
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use frhel\adventofcode2023php\Tools\Timer;

class Day7 extends Command
{
    protected static $day = 7;
    protected static $defaultName = 'Day7';
    protected static $defaultDescription = 'Advent of Code 2023 Solution';
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

        // Right answer: 
        $io->success('Part 1 Solution: ' .  $this->solve($data));

        // Right answer: 
        //$io->success('Part 2 Solution: ' .  $this->solve($data));
        
        $io->writeln('Total time: ' . $overallTimer->stop());  

        return Command::SUCCESS;
    }

    protected function solve($data) {
        $solution = 0;
        


        return $solution;
    }

    protected function parse_input($data) {        
        $data = preg_split('/\r\n|\r|\n/', file_get_contents($data));



        return $data;
    }

}