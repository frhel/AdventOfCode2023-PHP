<?php
// ----------------------------------------------------------------------------
// Problem description: https://adventofcode.com/2023/day/1
// Solution by: https://github.com/frhel (Fry)
// ----------------------------------------------------------------------------
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use frhel\adventofcode2023php\Tools\Timer;
use frhel\adventofcode2023php\Tools\Prenta;



class Day1
{
    protected $numbers;

    function __construct(private int $day) {

        $this->numbers = [
            "one" => "1",
            "two" => "2",
            "three" => "3",
            "four" => "4",
            "five" => "5",
            "six" => "6",
            "seven" => "7",
            "eight" => "8",
            "nine" => "9",
            "zero" => "0",
        ];
        
        $prenta = new Prenta();
        $overallTimer = new Timer();
        echo 'Day ' . $day . PHP_EOL;
        // The test data is so small we may as well just load both files in anyways
        $data_full = file_get_contents(__DIR__ . '/../../data/day_' . $day);
        $data_example = file_get_contents(__DIR__ . '/../../data/day_' . $day . '.ex');

        // Split by newline cross platform
        $data = preg_split('/\r\n|\r|\n/', $data_full);       

        // Right answer: 55607
        $prenta->answer($this->solve($data), 1);

        // Right answer: 55291
        $prenta->answer($this->solve($data, true), 2);

        // Stop the timer
        $time_done = $overallTimer->stop();
        $prenta->time($time_done, 'Overall Time');
    }

    protected function strpos_all($haystack, $needle) {

        $offset = 0;
        $allpos = array();
        while (($pos = strpos($haystack, $needle, $offset)) !== false) {
            $offset = $pos + 1;
            $allpos[$pos] = $needle;
        }
        return $allpos;

    }

    protected function merge_results($collection, $result) {

        foreach ($result as $pos => $val) {
            $collection[$pos] = $val;
        }
        return $collection;

    }

    protected function translate_nrs($str, $needles, $use_letters = false) {

        $found = [];
        foreach ($needles as $key => $value) {
            if (strpos($str, $value) !== false) {
                $found = $this->merge_results($found, $this->strpos_all($str, $value));                
            }

            if (!$use_letters) continue;

            if (strpos($str, $key) !== false) {
                $found = $this->merge_results($found, $this->strpos_all($str, $key));
            }
        }
        
        // Sort the results by position(key)
        ksort($found);

        return $found;

    }

    protected function solve($data, $use_letters = false) {

        $total = 0;
        $count = 0;

        foreach ($data as $key => $value) {
            $count++;
            $data[$key] = trim($value);
            $numbers_found = $this->translate_nrs($data[$key], $this->numbers, $use_letters);            
            if (count($numbers_found) < 1) {
                continue;
            }

            $firstIndex = array_key_first($numbers_found);
            $first = is_numeric($numbers_found[$firstIndex]) ? $numbers_found[$firstIndex] : $this->numbers[$numbers_found[$firstIndex]];
            $lastIndex = array_key_last($numbers_found);
            $last = is_numeric($numbers_found[$lastIndex]) ? $numbers_found[$lastIndex] : $this->numbers[$numbers_found[$lastIndex]];

            $total += intval("{$first}{$last}");
        }
        return $total;

    }

}