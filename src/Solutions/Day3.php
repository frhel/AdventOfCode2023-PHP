<?php

namespace frhel\adventofcode2023php\Solutions;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use frhel\adventofcode2023php\Tools\Timer;



class Day3 extends Command
{
    protected static $day = 3;
    protected static $defaultName = 'Day3';
    protected static $defaultDescription = 'Advent of Code 2023 Solution';
    protected $dataFile;
    protected $exampleFile;
    protected $dirs;
    protected $ex;

    function __construct() {
        $this->ex = 0;

        // Define the 8 directions around a symbol
        // We can use these by adding together the x and y coordinates of a direction
        // with the x and y coordinates of a grid position to get the coordinates of
        // the next position in that direction.
        // For example, if we want to go one step north, we add [0, 1] to the current
        // position. If we want to go one step south-east, we add [1, -1] to the current
        // position. Which works fine because ex. 5 + 1 = 6 and 5 - 1 = 4.
        $this->dirs = [
            'N' => [0, 1],
            'NE' => [1, 1],
            'E' => [1, 0],
            'SE' => [1, -1],
            'S' => [0, -1],
            'SW' => [-1, -1],
            'W' => [-1, 0],
            'NW' => [-1, 1],
        ];
        
        $this->dataFile = __DIR__ . '/../../data/day_' . self::$day;
        $this->exampleFile = __DIR__ . '/../../data/day_' . self::$day . '.ex';

        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        $overallTimer = new Timer();

        // $this->ex = 1;
        $data = $this->parse_input($this->ex === 1 ? $this->exampleFile : $this->dataFile);        

        $io->writeln(self::$defaultName . ' - ' . self::$defaultDescription);

        // Do both parts in one pass so we don't have to process the data twice
        $solution = $this->solve($data);

        // Right answer: 512794
        $io->success('Part 1 Solution: ' .  $solution[0]);

        // Right answer: 67779080
        $io->success('Part 2 Solution: ' .  $solution[1]);
        $io->writeln('Total time: ' . $overallTimer->stop());  

        return Command::SUCCESS;
    }

    protected function solve($data) {
        $parts = []; // Collect all the part numbers
        $gears = []; // Collect all the gear values from part 2

        [$symbols, $part_nrs] = $this->get_symbols_and_parts($data);

        // Go through each symbol, find the adjacent numbers from the part number collection
        foreach ($symbols as $symbol) {
            $numbers = $this->find_adjacent_numbers($symbol, $part_nrs, $data);
            if (count($numbers) < 1) { continue; } // No numbers found, skip symbol

            // If conditions for part 2 are met, add the gear value for this symbol to the gears collection
            if ($symbol['symbol'] === '*' && count($numbers) === 2) {
                $gears[] = $numbers[0] * $numbers[1];
            }           

            // Add the found numbers for the current symbol to the parts collection for part 1
            foreach ($numbers as $number) {
                $parts[] = $number;
            }

        }
        
        // Return both solutions at the same time
        return [array_sum($parts), array_sum($gears)];
    }

    protected function get_symbols_and_parts($grid) {
        $symbols = []; // Collect all the symbols
        $part_nrs = []; // Collect all the part numbers
        for ($y = 0; $y < count($grid); $y++) {
            $line = $grid[$y];

            for ($x = 0; $x < count($line); $x++) {
                $glyph = $line[$x]; // Process each glyph
                if ($glyph === '.') { continue; } // . is empty space, skip it
                
                // if glyph is a number, find the rest of the number and add it to part_nrs
                if (is_numeric($glyph)) {
                    // Save the number along with the start and end coordinates
                    [$number, $start, $end] = $this->get_whole_number($grid, $x, $y);
                    $part_nrs[] = ["part_nr" => $number, "start" => $start, "end" => $end];
                    $x = $end['x']; // continue from the end coordinate of the number so we don't count any digits twice
                } else {
                    // If the glyph is not a number or empty space, add it to the symbols collection along with its coordinates
                    $symbols[] = ["symbol" => $glyph, "x" => $x, "y" => $y];
                }
            }
        }
        // Return both collections at the same time
        return [$symbols, $part_nrs];
    }

    protected function get_whole_number($grid, $x, $y) {
        $number = ''; // Collect the number
        $start = ["x" => $x, "y" => $y]; // Save the start coordinates

        // Break the loop if we reach the end of the grid or if the next glyph is not a number
        while (            
            $x < count($grid[0])
            && $y < count($grid)
            && is_numeric($grid[$y][$x])
        ) {
            $number .= $grid[$y][$x]; // Add the current glyph to the number
            $x++;
        }
        // Save the end coordinates of the number
        // Using $x - 1 because we want the last coordinate of the number, not the first coordinate of the next glyph
        $end = ["x" => $x - 1, "y" => $y];

        // Return the number along with the start and end coordinates
        return [$number, $start, $end];
    }

    protected function find_adjacent_numbers($symbol, $part_nrs, $grid) {
        $numbers = []; // Collect the found numbers
        foreach ($this->dirs as $dir => $dir_coords) {
            // Here we check the 8 directions around the current symbol that are
            // defined in $this->dirs.
            $x = $symbol['x'] + $dir_coords[0];
            $y = $symbol['y'] + $dir_coords[1];

            // If the coordinates are out of bounds, we skip that direction.
            if ($x < 0 || $y < 0) { continue; }
            if ($x >= count($grid[0]) || $y >= count($grid)) { continue; }
            
            
            foreach ($part_nrs as $i => $part) {
                // If the coordinates are within the bounds of a part number, we add it to the numbers collection
                if (
                        $x >= $part["start"]["x"] 
                    &&  $x <= $part["end"]["x"] 
                    &&  $y >= $part["start"]["y"] 
                    &&  $y <= $part["end"]["y"]
                ) {
                    $numbers[] = $part["part_nr"];

                    // In order to not count the same number twice, we remove it from the part_nrs collection
                    // So that any other symbols that are adjacent to this number will not count it again.
                    unset($part_nrs[$i]);
                }
            }
        }
        return $numbers;
    }

    protected function parse_input($data) {        
        $data = preg_split('/\r\n|\r|\n/', file_get_contents($data));
        for ($i = 0; $i < count($data); $i++) {
            $data[$i] = str_split(trim($data[$i]));
        }

        return $data;
    }

}