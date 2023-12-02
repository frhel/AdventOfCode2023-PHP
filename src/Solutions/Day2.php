<?php
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use frhel\adventofcode2023php\Tools\Timer;



class Day2 extends Command
{
    protected static $day = 2;
    protected static $defaultName = 'Day2';
    protected static $defaultDescription = 'Advent of Code 2023 Solution';
    protected $dataFile;
    protected $dataFileEx;
    protected $colors;
    protected $io;

    function __construct($day = null) {
        if ($day) {
            self::$day = $day;
            self::$defaultName = 'Day ' . $day;
        }
        
        $this->dataFile = __DIR__ . '/../../data/day_' . self::$day;
        $this->dataFileEx = __DIR__ . '/../../data/day_' . self::$day . '.ex';

        $this->colors = [
            "red" => "12",
            "green" => "13",        
            "blue" => "14",
        ];

        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output) {
        system('clear');
        $io = new SymfonyStyle($input, $output);
        $overallTimer = new Timer();

        // Split by newline cross platform
        $data = preg_split('/\r\n|\r|\n/', file_get_contents($this->dataFile));
        // $data = preg_split('/\r\n|\r|\n/', file_get_contents($this->dataFileEx));
        $data = $this->parse_input($data);  
        
        $io->writeln(self::$defaultName . ' - ' . self::$defaultDescription);

        $solution = $this->solve($data);

        // Right answer: 2237
        $io->success('Part 1 Solution: ' .  $solution["viable_games"]);

        // Right answer: 66681
        $io->success('Part 2 Solution: ' .  $solution["game_powers"]);

        $io->writeln('Total time: ' . $overallTimer->stop());

        return Command::SUCCESS;
    }
    

    protected function solve($data) {

        $viable_games = [];
        $game_powers = [];

        foreach ($data as $game) {
            
            if ($this->is_viable($game)) {
                $viable_games[] = $game["id"];
            }

            $game = $this->find_max($game);
            $game_powers[] = $game["red"] * $game["green"] * $game["blue"];            
        }
        
        return [
            "game_powers" => array_sum($game_powers),
            "viable_games" => array_sum($viable_games),
        ];
    }

    protected function is_viable($game) {

        $viable = true;

        foreach ($game["rounds"] as $round) {

            $max = $this->find_max($game);

            if ($max["red"] > $this->colors["red"]
                || $max["green"] > $this->colors["green"]
                || $max["blue"] > $this->colors["blue"]
            ) {
                $viable = false;
                break;
            }
        }

        return $viable;
        
    }

    protected function parse_input($data) {

        $games = [];

        foreach ($data as $line) {

            $game_data = [
                "id" => 0,
                "rounds" => [],
            ];

            $first_split = explode(':', $line);
            $game_data["id"] = explode(' ', $first_split[0])[1];

            $subsets = explode('; ', $first_split[1]);

            foreach ($subsets as $subset) {
                $subset_split = explode(',', $subset);
                $rounds = [
                    "red" => 0,
                    "green" => 0,        
                    "blue" => 0,
                ];

                foreach ($subset_split as $colour) {
                    $colour = trim($colour);
                    $colour_split = explode(' ', $colour);
                    $rounds[$colour_split[1]] = $colour_split[0];                    
                }

                $game_data["rounds"][] = $rounds;
            }      
            
            $games[] = $game_data;

        }

        return $games;

    }


    protected function find_max($game) {
        
        $max = [
            "id" => $game["id"],
            "red" => 0,
            "green" => 0,
            "blue" => 0
        ];

        foreach($game["rounds"] as $round) {

            foreach ($round as $key => $value) {

                $max[$key] = max($max[$key], $value);

            }
        }       

        return $max;
    }

}