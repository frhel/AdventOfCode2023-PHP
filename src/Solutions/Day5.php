<?php
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use frhel\adventofcode2023php\Tools\Timer;

class Day5 extends Command
{
    protected static $day = 5;
    protected static $defaultName = 'Day5';
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

        // ====================================================================== //
        // ============================ Start Solving =========================== //
        // ====================================================================== //
        // Default to example data. Just comment out this line to use the real data.
        $this->ex = 1;
        $data = $this->parse_input($this->ex === 1 ? $data_example : $data_full);

        // Start the timer
        $overallTimer = new Timer();

        $solution = $this->solve($data);

        // Right answer: 282277027
        $io->success('Part 1 Solution: ' .  $solution[0]);

        // Right answer: 
        $io->success('Part 2 Solution: ' .  $solution[1]);
        
        $io->writeln('Total time: ' . $overallTimer->stop());  

        return Command::SUCCESS;
    }

    protected function solve($data) { 
        $part1 = 0;
        $part2 = 0;

        $seeds = $data['seeds'];
        $maps = $data['maps'];

        $pairs = array_map(fn($x) => [(int)$x, (int) $x], $seeds);
        print_r($pairs);

        foreach ($maps as $map) {
            $new_pairs = [];
            foreach ($map as $pair) {
                
            }
        }
        
        $pairs = $this->generate_pairs($seeds);
        print_r($pairs);

        print_r($maps);
        return [$part1, $part2];
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

            $processed['dest'] = $current_map[0][0] + $current_map[0][2];
            $processed['src'] = $current_map[0][0];
        }
        $maps[] = $processed;

        return ['seeds' => $seeds, 'maps' => $maps];
    }

}
