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

        // Default to example data. Just comment out this line to use the real data.
        // $this->ex = 1;
        $data = $this->parse_input($this->ex === 1 ? $data_example : $data_full);

        // ====================================================================== //
        // ============================ Start Solving =========================== //
        // ====================================================================== //
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
        $timer = new Timer();
        $part1 = 0;
        $part2 = 0;

        $seeds = $data['seeds'];
        $maps = $data['maps'];
        
        $locs = [];
        foreach ($seeds as $seed) {
            $locs[] = $this->find_map_locations($seed, $maps, 0, 0);
        }
        $part1 = min($locs);
        echo 'Part 1 Time: ' . $timer->stop() . PHP_EOL;


        $pairs = $this->generate_pairs($seeds);
        // $count = 0;
        // $locs = INF;
        // foreach ($pairs as $pair) {
        //     $seed_start = $pair[0];
        //     $seed_end = $seed_start + $pair[1];
        //     for ($i = $seed_start; $i <= $seed_end; $i++) {
        //         $locs = min($locs, $this->find_map_locations($i, $maps, 0, 0));
        //     }
        // }
        // $part2 = $locs;
        echo 'Part 2 Time: ' . $timer->stop() . PHP_EOL;


        //echo $this->find_destination(14) . PHP_EOL;

        return [$part1, $part2];
    }

    protected function generate_pairs($seeds) {
        $ranges = [];
        for ($i = 0; $i < count($seeds); $i++) {
            $ranges[] = [$seeds[$i], $seeds[$i] + $seeds[$i + 1]];
            $i++;            
        }

        for ($i = 0; $i < count($ranges); $i++) {
            $range = $ranges[$i];
            for ($j = $i + 1; $j < count($ranges); $j++) {
                $range2 = $ranges[$j];
                if ($range2[0] >= $range[0] && $range2[0] <= $range[1]) {
                    echo 'Range 2 is in range 1' . PHP_EOL;
                    if ($range2[1] <= $range[1]) {
                        unset($ranges[$i]);
                    } else if ($range2[1] > $range[1]) {
                        $ranges[$i][1] = $range2[1];
                    }
                }
            
            }
        }
        
        print_r($ranges);
        

        return $ranges;
    }

    protected function find_map_locations($seed, $maps, $map_key, $map_line) {
        if ($map_key >= count($maps)) return null;

        while (isset($maps[$map_key]) && $map_line < count($maps[$map_key])) {
        // while (isset($maps[$map_key]) && $map_line === 1) {
            $source = $maps[$map_key][$map_line][1];
            $destination = $maps[$map_key][$map_line][0];
            $length = $maps[$map_key][$map_line][2];

            $mapped_val = $this->find_destination($seed, $destination, $source, $length);

            if ($mapped_val !== $seed) {
                return $this->find_map_locations($mapped_val, $maps, $map_key + 1, 0) ?? $mapped_val;
            }
            
            $map_line++;
        }

        return $this->find_map_locations($seed, $maps, $map_key + 1, 0);       
    }

    protected function find_destination($seed, $destination, $source, $length) {
        $mapped_val = $seed;
        if ($seed <= $source + $length && $seed >= $source ) {
            // We are in the range of this map
            // We need to find the distance from source to destination
            $distance = $source - $destination;
            $mapped_val = $seed - $distance;
        }

        return $mapped_val;
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
        }
        $maps[] = $current_map;

        return ['seeds' => $seeds, 'maps' => $maps];
    }

}