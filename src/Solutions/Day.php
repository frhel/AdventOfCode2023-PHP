<?php

namespace frhel\adventofcode2023php\Solutions;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use frhel\adventofcode2023php\Tools\Timer;



class Day extends Command
{
    protected static $day = 0;
    protected static $defaultName = 'Template';
    protected static $defaultDescription = 'Advent of Code 2023 Solution';
    protected $dataFile;
    protected $exampleFile;

    function __construct($day = null) {
        if ($day) {
            self::$day = $day;
            self::$defaultName = 'Day ' . $day;
        }
        
        $this->dataFile = __DIR__ . '/../../data/day_' . self::$day . '.data';
        $this->exampleFile = __DIR__ . '/../../data/day_' . self::$day . '.example';

        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        $overallTimer = new Timer();

        $data = file_get_contents($this->dataFile);

        $io->writeln(self::$defaultName . ' - ' . self::$defaultDescription);


        $io->writeln('Total time: ' . $overallTimer->stop());        
        return Command::SUCCESS;
    }

}