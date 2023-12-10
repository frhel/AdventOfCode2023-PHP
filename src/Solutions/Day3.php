<?php
// ----------------------------------------------------------------------------
// Problem description: https://adventofcode.com/2023/day/3
// Solution by: https://github.com/frhel (Fry)
// Part 1: 512794
// Part 2: 67779080
// ----------------------------------------------------------------------------
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use frhel\adventofcode2023php\Tools\Timer;
use frhel\adventofcode2023php\Tools\Prenta;


class Day3 extends Day
{
    protected $dirs;
    function __construct(private int $day, $bench = 100, $ex = 0) {  // Define the 8 directions around a symbol
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

        parent::__construct($day, $bench, $ex);

    }


    public function solve($data) {
        $parts = []; // Collection of all the valid part numbers for part 1
        $gears = []; // Collection of all the gear values for part 2

        // Grab all the symbols and part numbers from the grid
        [$symbols, $part_nrs] = $this->get_symbols_and_parts($data);

        // Go through each symbol, find the adjacent numbers from the part number collection
        foreach ($symbols as $symbol) {
            $numbers = $this->find_adjacent_numbers($symbol, $part_nrs, $data);
            $number_count = count($numbers);
            if ($number_count < 1) { continue; } // No numbers found, skip symbol

            // If conditions for part 2 are met, add the gear value for this symbol to the gears collection
            if ($symbol['symbol'] === '*' && $number_count === 2) {
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

    protected function get_symbols_and_parts($data) {
        $symbols = []; // Collect all the symbols
        $part_nrs = []; // Collect all the part numbers
        for ($y = 0; $y < count($data[0]); $y++) {
            $line = $data[$y];

            for ($x = 0; $x < count($data[0]); $x++) {
                $glyph = $line[$x]; // Process each glyph
                if ($glyph === '.') { continue; } // . is empty space, skip it
                
                // if glyph is a number, find the rest of the number and add it to part_nrs
                if (is_numeric($glyph)) {
                    // Save the number along with the start and end coordinates
                    [$number, $start, $end] = $this->get_whole_number($x, $y, $data);       
                    $x = $end['x']; // continue from the end coordinate of the number so we don't count any digits twice
                    if ($number === null) { continue; } // If the number is not valid(no adjacent symbols), skip it
                    $part_nrs[] = ["part_nr" => $number, "start" => $start, "end" => $end];
                } else {
                    // If the glyph is not a number or empty space, add it to the symbols collection along with its coordinates
                    if (!$this->has_grid_neighbour('symbol', $x, $y, $data)) { continue; } // If the symbol is not adjacent to a number, skip it
                    $symbols[] = ["symbol" => $glyph, "x" => $x, "y" => $y];
                }
            }
        }
        // Return both collections at the same time
        return [$symbols, $part_nrs];
    }

    protected function get_whole_number($x, $y, $data) {
        $number = ''; // Collect the number to a string
        $start = ["x" => $x, "y" => $y]; // Save the start coordinates
        $has_neighbour = false;

        // Break the loop if we reach the end of the grid or if the next glyph is not a number
        do {
            // If the current glyph is a number, check if it has a grid neighbour that is a symbol
            if (!$has_neighbour && $this->has_grid_neighbour('nr', $x, $y, $data)) { $has_neighbour = true; }
            $number .= $data[$y][$x]; // Add the current glyph to the number
            $x++;
        } while (            
            $x < count($data[0])
            && is_numeric($data[$y][$x])
        );
        // Save the end coordinates of the number
        // Using $x - 1 because we want the last coordinate of the number, not the first coordinate of the next glyph
        $end = ["x" => $x - 1, "y" => $y];


        // Return the number along with the start and end coordinates
        if ($has_neighbour) { 
            return [$number, $start, $end];
        }
        return [null, $start, $end];
    }

    protected function find_adjacent_numbers($symbol, $part_nrs, $data) {
        $numbers = []; // Collect the found numbers
        foreach ($this->dirs as $dir_coords) {
            // Here we check the 8 directions around the current symbol that are
            // defined in $this->dirs.
            $x = $symbol['x'] + $dir_coords[0];
            $y = $symbol['y'] + $dir_coords[1];
 
            // If the coordinates are out of bounds, we skip that direction.
            if ($x < 0 || $y < 0) { continue; }
            if ($x >= count($data[0]) || $y >= count($data)) { continue; }            
            
            foreach ($part_nrs as $i => $part) {
                // If the number we are checking is below the current symbol, we skip it
                if ($symbol["y"] > $part["start"]["y"] + 1) { continue; }
                // If the number we are checking is above the current symbol, we break the loop
                // as there is no need to check any more numbers since they are sorted low y to high y
                if ($symbol["y"] < $part["end"]["y"] - 1) { break; }
                    
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

    protected function has_grid_neighbour($type, $x, $y, $data) {
        foreach ($this->dirs as $dir => $dir_coords) {
            $temp_x = $x + $dir_coords[0];
            $temp_y = $y + $dir_coords[1];


            if ($temp_x < 0 || $temp_y < 0) { continue; }
            if ($temp_x >= count($data[0]) || $temp_y >= count($data)) { continue; }

            if ($data[$temp_y][$temp_x] === '.') { continue; }

            if (
                    ($type === 'nr' && !is_numeric($data[$temp_y][$temp_x]))
                ||  ($type === 'symbol' && is_numeric($data[$temp_y][$temp_x]))
            ) {
                return true; 
            }
        }
        return false;
    }

    protected function parse_input($data) {        
        $data = preg_split('/\r\n|\r|\n/', $data);
        for ($i = 0; $i < count($data); $i++) {
            $data[$i] = str_split(trim($data[$i]));
        }

        return $data;
    }

}