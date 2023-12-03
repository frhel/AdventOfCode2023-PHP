<?php

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
    protected $dataFile;
    protected $exampleFile;
    protected $ex;

    function __construct() {
        $this->ex = 0;

        $this->dataFile = __DIR__ . '/../../data/day_' . self::$day;
        $this->exampleFile = __DIR__ . '/../../data/day_' . self::$day . '.ex';

        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        $overallTimer = new Timer();

        // Default to example data. Just comment out this line to use the real data.
        $this->ex = 1;
        $data = $this->parse_input($this->ex === 1 ? $this->exampleFile : $this->dataFile);

        $io->writeln(self::$defaultName . ' - ' . self::$defaultDescription);

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
        $data = preg_split('/\r\n|\r|\n/', file_get_contents($this->dataFile));
        return $data;
    }

}