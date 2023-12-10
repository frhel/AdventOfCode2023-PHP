<?php
// ----------------------------------------------------------------------------
// Problem description: https://adventofcode.com/2023/day/2
// Solution by: https://github.com/frhel (Fry)
// Part 1: 2237
// Part 2: 66681
// ----------------------------------------------------------------------------
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use frhel\adventofcode2023php\Tools\Timer;
use frhel\adventofcode2023php\Tools\Prenta;



class Day2 extends Day
{
    protected $colors;
    function __construct(private int $day, $bench = 100, $ex = 0) {  
        $prenta = new Prenta();
        $this->colors = [
            "red" => "12",
            "green" => "13",        
            "blue" => "14",
        ];   
        parent::__construct($day, $bench, $ex);
    }        

    public function solve($games) {
        $viable_games = [];
        $game_powers = [];

        foreach ($games as $game) {            
            if ($this->is_viable($game)) {
                $viable_games[] = $game["id"];
            }

            $max_color_vals = $this->find_max_color_values($game);
            $game_powers[] = $max_color_vals["red"] * $max_color_vals["green"] * $max_color_vals["blue"];            
        }
        
        return [array_sum($viable_games), array_sum($game_powers)];
    }

    protected function is_viable($game) {
        $viable = true;

        foreach ($game["rounds"] as $round) {
            if ($round["red"] > $this->colors["red"]
                || $round["green"] > $this->colors["green"]
                || $round["blue"] > $this->colors["blue"]
            ) {
                $viable = false;
                break;
            }
        }

        return $viable;        
    }

    protected function find_max_color_values($game) {        
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

    protected function parse_input($data) {        
        $data = preg_split('/\r\n|\r|\n/', $data);
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
                $round = [
                    "red" => 0,
                    "green" => 0,        
                    "blue" => 0,
                ];

                foreach ($subset_split as $colour) {
                    $colour = trim($colour);
                    $colour_split = explode(' ', $colour);
                    $round[$colour_split[1]] = $colour_split[0];                    
                }

                $game_data["rounds"][] = $round;
            }      
            
            $games[] = $game_data;
        }

        return $games;
    }
}