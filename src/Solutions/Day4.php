<?php
// ----------------------------------------------------------------------------
// Problem description:
// Solution by: https://github.com/frhel (Fry)
// Part 1: 15205
// Part 2: 6189740
// ----------------------------------------------------------------------------
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use Ds\Vector as vec;

use frhel\adventofcode2023php\Tools\Timer;
use frhel\adventofcode2023php\Tools\Prenta;

class Day4 extends Day
{
    function __construct(private int $day, $bench = 100, $ex = 0) {     
        parent::__construct($day, $bench, $ex);
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
    public function solve($cards) {
        $point_sum = 0;
        $total_cards = 0;

        foreach ($cards as $key => $card) {
            $winners = $card[0];
            $numbers = $card[1];

            // Count the wins for this card once
            $win_count = 0;
            foreach ($winners as $winner)
                if (in_array($winner, $numbers))
                    $win_count++;
            
            // If the card has more than one win, it's worth 2^(wins-1) points
            // otherwise it's worth $win_count points (1 or 0)
            $points = $win_count > 1 ? pow(2, $win_count - 1) : $win_count;
            
            // For every copy of this card, add the number of wins to the count of the next $j cards
            // That way we keep track of how many cards are in the game as we go
            for ($j = 1; $j <= $win_count; $j++) {
                (int) $tkey = $key + $j;
                if ($tkey >= count($cards)) break;
                $cards[$tkey][2] = $cards[$tkey][2] + $cards[$key][2];
            }

            // Add the points for this card to the total sum of points
            $point_sum += $points; 
            // Add the number of copies of this card to the total number of cards
            $total_cards += $cards[$key][2];
        }

        // Return both answers at the same time
        return [$point_sum, $total_cards];
    }

    /**
     * Parses the input file into an array of cards with winners, numbers, and copy count
     * 
     * @param string $data
     * @return array
     */
    protected function parse_input($data) {        
        $data = preg_split('/\r\n|\r|\n/', $data);
        $data_out = [];
                
        foreach ($data as $key => $card) {
            $card = explode(' | ', explode(': ', $card)[1]);

            $data_out[] = [
                $this->parse_numbers(explode(' ', $card[0])),
                $this->parse_numbers(explode(' ', $card[1])),
                1
            ];
        }
        return $data_out;
    }

    protected function parse_numbers($data) {
        foreach ($data as $idx => $number) {
            if ($number === '' || $number === ' ') {
                unset($data[$idx]);
                continue;
            }
            $data[$idx] = (int) trim($number);
        }
        return $data;
    }
}