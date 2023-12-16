<?php
// ----------------------------------------------------------------------------
// Problem description: https://adventofcode.com/2023/day/13
// Solution by: https://github.com/frhel (Fry)
// Part1: 36448
// Part2: 35799
// ----------------------------------------------------------------------------
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use frhel\adventofcode2023php\Tools\Timer;
use frhel\adventofcode2023php\Tools\Prenta;
use frhel\adventofcode2023php\Tools\Utils;

class Day13 extends Day
{
    function __construct(private int $day, $bench = 1000, $ex = 0) {
        // $ex = 1;
        parent::__construct($day, $bench, $ex);
    }


    /**
     * Solves the problem. Needs to be public so we can call it from the benchmarking code
     *
     * @param array $data The data to solve
     * @return array The solution to the problem in the form of [part1, part2]
     */
    public function solve($data, $part1 = 0, $part2 = 0) {
        $patterns = $this->parse_input($this->load_data($this->day, $this->ex)); $this->data = $data;

        foreach ($patterns as $pattern) {
            [$p1, $p2] = $this->find_mirror_pattern($pattern);
            $part1 += $p1;
            $part2 += $p2;
        }

        return [$part1, $part2];
    }

    protected function find_mirror_pattern($pattern, $dir = 0, $part1 = 0, $part2 = 0) {
        // $dir = 0 => rows, 1 => cols
        $lines = $pattern[$dir];

        // Run through all the lines, starting with the 2nd one so we don't have to make
        // an out of bounds check for comparisons
        for ($L = 1; $L < count($lines); $L++) {
            // $c is the offset // $diff is the number of differences across the pattern we are testing // $i is the line we are testing
            $c = 0;   $diff = 0;    $i = $L;
            // Run through the pattern, incrementing the offset and testing in both directions until we find a difference or
            // reach the end of the pattern. If we reach the boundary in either direction, we found a mirror pattern
            while ($i - $c - 1 >= 0 && $i + $c < count($lines)) {
                // XOR the two lines and count the number of differences
                $diff += substr_count(decbin($lines[$i - $c - 1] ^ $lines[$i + $c]), '1');
                // Continue the outer loop if we have more than one difference, skipping the rest of the inner loop
                // as well as the rest of the outer loop
                if ($diff > 1) continue 2;

                $c++;
            }
            // If we are in the rows, multiply the line number by 100 to get the right answer increment.
            $i = $dir < 1 ? $i * 100 : $i;
            $part1 = $diff < 1 ? $i : $part1; // If we have no differences, we have found a pattern for part 1
            $part2 = $diff === 1 ? $i : $part2; // If we have exactly one difference, we have found a pattern for part 2
            if ($part1 > 0 && $part2 > 0) return [$part1, $part2]; // Return early if we have found both solutions
        }

         // If we haven't found a solution yet, try the columns by incrementing $dir and recursing
        if ($dir === 0)  return $this->find_mirror_pattern($pattern, ++$dir, $part1, $part2);
    }



    /**
     * Parses the input data into a usable format
     *
     * @param string $data The input data
     * @return array The parsed data
     */
    protected function parse_input($data) {
        $data = array_map(fn($block) =>
            explode("\n", $block), explode("\n\n", $data)
        );

        $hash = ['.' => 0, '#' => 1];
        $out = [];
        foreach ($data as $key => $pattern) {
            $rows = [];    $cols = [];
            // Change all the "." to 0 and "#" to 1 and convert the binary strings to integers for easy diffing later
            foreach ($pattern as $key => $line) {
                $rows[] = str_replace(['.', '#'], ['0', '1'], $line);
                for ($key = 0; $key < strlen($line); $key++)
                    $cols[$key][] = $hash[$line[$key]];
            }
            $rows = array_map('bindec', $rows);
            $cols = array_map(fn($col) => bindec(implode('', $col)), $cols);
            $out[] = [0 => $rows, 1 => $cols];
        }
        return $out;
    }
}