<?php
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use frhel\adventofcode2023php\Tools\Timer;

class Day6 extends Command
{
    protected static $day = 6;
    protected static $defaultName = 'Day6';
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
        // $this->ex = 1;
        $data = $this->parse_input($this->ex === 1 ? $data_example : $data_full);

        // ====================================================================== //
        // ============================ Start Solving =========================== //
        // ====================================================================== //
        // Start the timer
        $overallTimer = new Timer();

        $solution = $this->solve($data);

        // Right answer: 
        $io->success('Part 1 Solution: ' .  $solution[0]);

        // Right answer: 
        $io->success('Part 2 Solution: ' .  $solution[1]);
        
        $io->writeln('Total time: ' . $overallTimer->stop());  

        return Command::SUCCESS;
    }

    protected function solve($data) {
        $win_multiplier = 1;
        for ($i = 0; $i < count($data['time']); $i++) {
            $time = $data['time'][$i];
            $distance = $data['distance'][$i];
            $speed = 0;
            
            $win_count = $this->get_win_count($time, $distance);

            echo 'Time: ' . $time . ' Distance: ' . $distance . ' Speed: ' . $speed . ' Win count: ' . $win_count . PHP_EOL;

            $win_multiplier *= $win_count;
        }
        
        $time = (int) join('',$data['time']);
        $distance = (int) join('',$data['distance']);

        $win_count = $this->get_win_count($time, $distance);

        return [$win_multiplier, $win_count];
    }

    protected function get_win_count($time, $distance) {        
        $increment = 1;
        $speed = 0;
        $win_count = 0;

        for ($i = 0; $i < $time; $i++) {
            $speed += $increment;
            $remaining_time = $time - $i - 1;
            if ($speed * $remaining_time > $distance) {
                $win_count++;
            }
        }

        return $win_count;
    }

    protected function parse_input($data) {        
        $data = preg_split('/\r\n|\r|\n/', $data);

        $out['time'] = $data[0];
        $out['distance'] = $data[1];

        $out['time'] = explode(' ', $out['time']);
        $out['distance'] = explode(' ', $out['distance']);

        foreach($out as $k => $v) {
            foreach($v as $k2 => $v2) {
                if (!is_numeric($v2)) {
                    unset($out[$k][$k2]);
                }
            }
        }

        $out['time'] = array_values($out['time']);
        $out['distance'] = array_values($out['distance']);

        return $out;
    }

}