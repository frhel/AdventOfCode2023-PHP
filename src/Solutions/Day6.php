<?php
// ----------------------------------------------------------------------------
// Problem description: https://adventofcode.com/2023/day/6
// Solution by: https://github.com/frhel (Fry)
// ----------------------------------------------------------------------------
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use frhel\adventofcode2023php\Tools\Timer;
use frhel\adventofcode2023php\Tools\Prenta;

class Day6
{

    function __construct(private int $day) {
        $prenta = new Prenta();
        $ex = 0;

        // The test data is so small we may as well just load both files in anyways
        $data_full = file_get_contents(__DIR__ . '/../../data/day_' . $day);
        $data_example = file_get_contents(__DIR__ . '/../../data/day_' . $day . '.ex');

        // $ex = 1;
        $data = $this->parse_input($ex === 1 ? $data_example : $data_full);
        
        // ====================================================================== //
        // ============================ Start Solving =========================== //
        // ====================================================================== //
        // Start the timer
        $overallTimer = new Timer();

        // Solve both parts at the same time. See solve() docblock for more info
        $solution = $this->solve($data);

        // Right answer: 25200
        $prenta->answer($solution[0], 1);

        // Right answer: 36992486
        $prenta->answer($solution[1], 2);
 
        // Stop the timer
        $time_done = $overallTimer->stop();
        $prenta->time($time_done, 'Overall Time');
    }

    /**
     * Solves both parts of the problem at the same time
     * 
     * @param array $cards
     * @return array [part1, part2]
     * 
     * Part 1: The sum of all points for all cards
     * Part 2: The total number of cards and card copies in the game
     */
    protected function solve($data) {
        $win_multiplier = 1;
        for ($i = 0; $i < count($data['time']); $i++) {            
            $win_count = $this->get_win_count($data['time'][$i], $data['distance'][$i]);
            $win_multiplier *= $win_count;
        }
        
        $time = (int) join('',$data['time']);
        $distance = (int) join('',$data['distance']);

        $win_count = $this->get_win_count($time, $distance);

        return [$win_multiplier, $win_count];
    }

    protected function get_win_count($time, $distance) {        
        $increment = 1;
        $speed = 0;
        $win_count = 0;

        for ($i = 0; $i < $time; $i++) {
            $speed += $increment;
            if ($speed * ($time - $i - 1) > $distance) {
                $win_count++;
            }
        }

        return $win_count;
    }

    protected function parse_input($data) {        
        $data = preg_split('/\r\n|\r|\n/', $data);

        $out['time'] = $data[0];
        $out['distance'] = $data[1];

        $out['time'] = explode(' ', $out['time']);
        $out['distance'] = explode(' ', $out['distance']);

        foreach($out as $k => $v) {
            foreach($v as $k2 => $v2) {
                if (!is_numeric($v2)) {
                    unset($out[$k][$k2]);
                }
            }
        }

        $out['time'] = array_values($out['time']);
        $out['distance'] = array_values($out['distance']);

        return $out;
    }

}