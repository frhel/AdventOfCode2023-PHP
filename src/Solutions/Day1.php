<?php

namespace frhel\adventofcode2023php\Solutions;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use frhel\adventofcode2023php\Tools\Timer;



class Day1 extends Command
{
    protected static $day = 1;
    protected static $defaultName = 'Day1';
    protected static $defaultDescription = 'Advent of Code 2023 Solution';
    protected $dataFile;
    protected $dataFileEx;
    protected $letters;

    function __construct($day = null) {
        if ($day) {
            self::$day = $day;
            self::$defaultName = 'Day ' . $day;
        }
        
        $this->dataFile = __DIR__ . '/../../data/day_' . self::$day;
        $this->dataFileEx = __DIR__ . '/../../data/day_' . self::$day . '.ex';

        $this->letters = [
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

        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output) {
        system('clear');
        $io = new SymfonyStyle($input, $output);
        $overallTimer = new Timer();

        // Split by newline cross platform
        $data = preg_split('/\r\n|\r|\n/', file_get_contents($this->dataFile));       
        
        $io->writeln(self::$defaultName . ' - ' . self::$defaultDescription);

        $io->success('Part 1 Solution: ' . $this->solve($data));

        $io->success('Part 2 Solution: ' . $this->solve($data, true));

        $io->writeln('Total time: ' . $overallTimer->stop());
        return Command::SUCCESS;
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
            $numbers_found = $this->translate_nrs($data[$key], $this->letters, $use_letters);            
            if (count($numbers_found) < 1) {
                continue;
            }

            $firstIndex = array_key_first($numbers_found);
            $first = is_numeric($numbers_found[$firstIndex]) ? $numbers_found[$firstIndex] : $this->letters[$numbers_found[$firstIndex]];
            $lastIndex = array_key_last($numbers_found);
            $last = is_numeric($numbers_found[$lastIndex]) ? $numbers_found[$lastIndex] : $this->letters[$numbers_found[$lastIndex]];

            $total += intval("{$first}{$last}");
        }
        return $total;
    }

}