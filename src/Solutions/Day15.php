<?php
// ----------------------------------------------------------------------------
// Problem description: https://adventofcode.com/2023/day/15
// Solution by: https://github.com/frhel (Fry)
// Part1: 501680
// Part2: 241094
// ----------------------------------------------------------------------------
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use frhel\adventofcode2023php\Tools\Timer;
use frhel\adventofcode2023php\Tools\Prenta;
use frhel\adventofcode2023php\Tools\Utils;

class Day15 extends Day
{
    private $boxes;
    function __construct(private int $day, $bench = 1000, $ex = 0) {
        // $ex = 1;
        $this->boxes = [];
        parent::__construct($day, $bench, $ex);
    }


    /**
     * Solves the problem. Needs to be public so we can call it from the benchmarking code
     *
     * @param array $data The data to solve
     * @return array The solution to the problem in the form of [part1, part2]
     */
    public function solve($data, $part1 = 0, $part2 = 0) {
        $data = $this->parse_input($this->load_data($this->day, $this->ex)); $this->data = $data;

        foreach($data as $str) $part1 += $this->calc_val($str);

        $part2 = $this->calc_lens_power($data);

        // return ['Redacted', 'Redacted'];
        return [$part1, $part2];
    }

    private function calc_lens_power($data) {
        // Have started defining these outside the loops out of habit at this point
        // sometimes it seems to provides a small speed boost, sometimes it doesn't
        [$power, $label, $box_nr, $lens, $last_c] = [0, '', 0, 0, ''];
        foreach ($data as $str) {
            $last_c = substr($str, strlen($str) -1,1);
            if ($last_c === '-') {
                $label = substr($str, 0, strlen($str) - 1);
                $box_nr = $this->calc_val($label);
                unset($this->boxes[$box_nr][$label]); // remove the element by name
            } else {
                $label = substr($str, 0, strlen($str) - 2);
                $box_nr = $this->calc_val($label);
                $lens = $last_c;
                $this->boxes[$box_nr][$label] = $lens; // set the element by name
            }
        }

        // All the things are added, calculate the lens power
        foreach ($this->boxes as $nr => $box) {
            $box_power = 1 + $nr;
            $slot_nr = 1;
            foreach ($box as $lens)
                $power += $box_power * $slot_nr++ * $lens;
        }
        return $power;
    }

    private function calc_val($str) {
        $last = 0;
        for ($i = 0; $i < strlen($str); $i++) {
            $c = ord($str[$i]) + $last;
            $c *= 17;
            $last = $c % 256;
        }
        return $last;
    }

    /**
     * Parses the input data into a usable format
     *
     * @param string $data The input data
     * @return array The parsed data
     */
    protected function parse_input($data) {
        $data = preg_split('/\r\n|\r|\n/', $data);

        return explode(',', $data[0]);
    }
}