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
    function __construct(private int $day, $bench = 0, $ex = 0) {
        $ex = 1;
        parent::__construct($day, $bench, $ex);
    }


    /**
     * Solves the problem. Needs to be public so we can call it from the benchmarking code
     *
     * @param array $data The data to solve
     * @return array The solution to the problem in the form of [part1, part2]
     */
    public function solve($data) {
        $patterns = $this->parse_input($this->load_data($this->day, $this->ex)); $this->data = $data;

        $part1 = 0;
        $part2 = 0;

        $maps = file_get_contents(__DIR__ . "/../../data/day_13");
        $maps = explode("\n\n", trim($maps));

        $result = $part1 = $part2 = 0;

        // foreach ($maps as $m => $map)
        // {
        //     $map = explode("\n", $map);
        //     $map = array_map(fn($r)=>str_split(str_replace(["#", "."], [1, 0], $r)), $map);

        //     foreach (["H" => 100, "V" => 1] as $reflect => $mult)
        //     {
        //         if ($reflect == "V") $map = array_map(null, ...$map);

        //         for ($h = count($map), $r = 0; $r < $h - 1; $r++)
        //         {
        //             $diff = 0;
        //             for ($i = 0; $r - $i >= 0 && $r + 1 + $i < $h; $i++)
        //             {
        //                 $lhs = bindec(implode($map[$r - $i]));
        //                 $rhs = bindec(implode($map[$r + 1 + $i]));
        //                 if (($diff += substr_count(decbin($lhs ^ $rhs), "1")) > 1) break;
        //             }
        //             if ($diff == 0) $part1 += $mult * ($r + 1);
        //             if ($diff == 1) $part2 += $mult * ($r + 1);
        //         }
        //     }
        // }
        // return [$part1, $part2];

        foreach ($patterns as $pattern) {
            $rows = $pattern['rows'];
            $cols = $pattern['cols'];

            [$p1r, $p2r] = $this->find_mirror_pattern($rows);
            [$p1c, $p2c] = $this->find_mirror_pattern($cols);
            $part1 += $p1r > 0 ? $p1r * 100 : $p1c;
            $part2 += $p2r > 0 ? $p2r * 100 : $p2c;
        }

        return [$part1, $part2];
    }

    protected function find_mirror_pattern($lines, $smudge = false) {
        $potential = $this->potential_mirrors($lines, $smudge);

        [$part1, $part2] = [0, -1];
        foreach ($potential as $idx) {
            $part1 = $part1 < 1 ? 0 : $part1;
            $part2 = $part2 < 1 ? -1 : $part2;
            $offset = 0;
            while ($idx - $offset - 1 >= 0 && $idx + $offset < count($lines)) {
                [$l1, $l2] = [$lines[$idx - $offset - 1], $lines[$idx + $offset]];
                if ($l1 === $l2) {
                    $offset++;
                } else if ($this->find_smudge($l1, $l2) && $part2 === (-1)) {
                    $offset++;
                    $part2 = 0;
                    $part1 = -1;
                } else if ($l1 !== $l2 && $part2 === 0) {
                    $part2 = -1;
                    $part1 = -1;
                    $offset = 0;
                    break;
                }

            }
            if ($offset > 0) {
                if ($part1 < 1) $part1 = $part1 === 0 ? $idx : 0;
                if ($part2 < 1) $part2 = $part2 === 0 ? $idx : 0;
                if ($part1 > 0 && $part2 > 0) return [$part1, $part2];
            }
        }
        echo 'Part 1 '. $part1 .', Part2 '. $part2 .''.PHP_EOL;
        return [$part1, $part2];
    }

    protected function potential_mirrors($lines, $smudge = false) {
        $potential = [];
        foreach ($lines as $key => $line) {
            if ($key - 1 < 0) continue;
            if ($smudge && $this->find_smudge($lines[$key - 1], $line)) $potential[] = $key;
            if ($line === $lines[$key - 1]) $potential[] = $key;
        }
        return $potential;
    }

    protected function find_smudge($str1, $str2) {
        $temp = $str1;
        $found = false;
        for ($c = 0; $c < strlen($str1); $c++) {
            $temp[$c] = $str1[$c] === '#' ? '.' : '#';
            if ($temp === $str2) {
                if ($found === true) return false;
                else $found = true;
            }
            $temp[$c] = $str1[$c];
        }
        return $found;
    }



    /**
     * Parses the input data into a usable format
     *
     * @param string $data The input data
     * @return array The parsed data
     */
    protected function parse_input($data) {
        $data = preg_split('/\r\n|\r|\n/', $data);

        // Split array into patterns, separated by empty lines
        $patterns = [];
        $pattern = 0;
        foreach ($data as $key => $line) {
            if (strlen($line) === 0) {
                $pattern++;
                continue;
            }
            $patterns[$pattern][] = $line;
        }


        $out = [];
        foreach ($patterns as $key => $pattern) {
            $rows = [];
            $cols = array_fill(0, strlen($pattern[0]), '');
            foreach ($pattern as $key => $line) {
                $rows[] = trim($line);
                foreach (str_split($line) as $key => $char) {
                    $cols[$key] .= $char;
                }
            }
            $out[] = ['rows' => $rows, 'cols' => $cols];
        }

        return $out;
    }
}