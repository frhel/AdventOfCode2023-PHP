<?php
// ----------------------------------------------------------------------------
// Problem description: https://adventofcode.com/2023/day/7
// Solution by: https://github.com/frhel (Fry)
// Part 1: 247823654
// Part 2: 245461700
// ----------------------------------------------------------------------------
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use frhel\adventofcode2023php\Tools\Timer;
use frhel\adventofcode2023php\Tools\Prenta;

class Day7 extends Day
{
    protected $hand_strengths;
    protected $card_strengths;
    function __construct(private int $day, $bench = 100, $ex = 0) {     
        $this->hand_strengths = [51 => 7, 42 => 6, 32 => 5, 33 => 4, 23 => 3, 24 => 2, 15 => 1];
        $this->card_strengths = [
            '2' => 1, '3' => 2, '4' => 3, '5' => 4, '6' => 5, '7' => 6, '8' => 7, '9' => 8, 
            'T' => 9, 'J' => 10, 'Q' => 11, 'K' => 12, 'A' => 13
        ];
        parent::__construct($day, $bench, $ex);
    }    

    /**
     * Solves the problem
     * 
     * @param array $data The data to solve
     * @return array The solution to the problem in the form of [part1, part2]
     */
    public function solve($data) {
        $hands = $data;

        $joker = false;
        $rated_hands = $this->rate_hand_strengths($hands, $joker);
        $part1 = $this->score_hands($rated_hands, $joker);


        $joker = true;
        $this->adjust_card_strengths_for_joker(); // Modify the card strengths list to account for jokers
        $rated_hands = $this->rate_hand_strengths($hands, $joker);
        $part2 = $this->score_hands($rated_hands, $joker);
        

        return [$part1, $part2];
    }

    /**
     * Rate the hands based on the hand strength list
     * 
     * @param array $hands
     * @param bool $joker
     * @return array ['cards' => string, 'bid' => int, 'strength' => int]
     */
    protected function rate_hand_strengths($hands, $joker) {
        $h_strengths = $this->hand_strengths;

        foreach ($hands as $key => $hand) {            
            $cards = $hand['cards'];
            
            // Counts the number of each card and sorts them from highest to lowest
            $cards_map = array_count_values($cards);            
            arsort($cards_map);

            // Check if any of the cards are jokers
            if ($joker && isset($cards_map['J']) && $cards_map['J'] !== 5) {
                // We have a joker, so we only need its value from here on out
                $joker_count = $cards_map['J'];
                unset($cards_map['J']); // Yeet!

                // Grab all entries with highest identical values
                $highest_values = [];
                foreach ($cards_map as $ckey => $value) {
                    if ($value === $cards_map[array_key_first($cards_map)]) $highest_values[$ckey] = $value;
                }
                
                // Grant the top dog the joker count
                $cards_map[array_key_first($highest_values)] += $joker_count;
            }

            // Set the strength of the hand and return it to the array
            $str_key = $cards_map[array_key_first($cards_map)] . count($cards_map);
            $hands[$key]['strength'] = $h_strengths[$str_key];
        }

        return $hands;
    }

    /**
     * Score the hands based on their strength and bid
     * 
     * @param array $hands
     * @param bool $joker
     * @return int The final score
     */
    protected function score_hands($hands, $joker) {
        $score = 0;

        $hands = $this->sort_hands_by_abs_strength($hands, $joker);

        // Reverse the array
        $hands = array_reverse($hands);

        // Sum it all up
        foreach ($hands as $key => $hand) {
            $rank = $key + 1;
            $score += $hand['bid'] * $rank;
        }
        return $score;
    }

    /**
     * Sort the hands by their absolute strength
     * Takes into account the strength and order of the cards
     * in the hand on top of the hand strength
     * 
     * @param array $hands
     * @param bool $joker
     * @return array ['cards' => string, 'bid' => int, 'strength' => int]
     */
    protected function sort_hands_by_abs_strength($hands, $joker) {
        usort($hands, function($a, $b) use ($joker) {
            // These values only come into play if we have two hands with the same strength
            // They add the strength difference of the cards from left to right to the hand strength
            // for evaluation
            $a_add = 0;
            $b_add = 0;
            if ($a['strength'] === $b['strength']) {
                // Find the highest card starting from the left
                [$a_add, $b_add] = $this->find_stronger_hand($a, $b);
            }

            // Since the X_add values are initialized to 0, we can just add them to the strength
            // no matter if we need them or not
            return ($b['strength'] + $b_add) - ($a['strength'] + $a_add);  
        });
        return $hands;
    }

    /**
     * Find the stronger hand of two equally rated hands
     * by comparing the card strengths from left to right
     * 
     * @param array $a
     * @param array $b
     * @param bool $joker
     * @return array [int, int]
     */
    protected function find_stronger_hand($a, $b) {
        $c_strengths = $this->card_strengths;

        $a_cards = $a['cards'];
        $b_cards = $b['cards'];

        $a_add = 0;
        $b_add = 0;

        $i = 0;
        while ($i < 5) {
            // If the cards are equal, we can skip to the next card
            if ($c_strengths[$a_cards[$i]] === $c_strengths[$b_cards[$i]]) {
                $i++;
                continue;
            }

            // We found a difference
            // Save the strength values of the two cards and break
            $a_add = $c_strengths[$a_cards[$i]];
            $b_add = $c_strengths[$b_cards[$i]];
            break;
        }

        return [$a_add, $b_add];
    }

    /**
     * Adjust the card strengths list to account for jokers
     * We do this by adding 1 to the strength of every card up to 10
     * and setting the strength of the joker to 1
     * 
     * @return void
     */
    protected function adjust_card_strengths_for_joker() {
        $c_strengths = $this->card_strengths;
        foreach ($c_strengths as $key => $value) {
            if ($value === 10) {
                // Change the joker to 1 and break
                // we don't need to increment the rest of the cards
                $c_strengths[$key] = 1;
                break;
            }
            
            // Increment the card strength if it's not a joker
            $c_strengths[$key] = $value + 1;
        }
        
        // Mutate the card strengths list directly
        $this->card_strengths = $c_strengths;
    }

    /**
     * Parse the input data into a usable format
     * 
     * @param string $data
     * @return array ['cards' => string, 'bid' => int, 'strength' => int]
     */
    protected function parse_input($data) {        
        $data = preg_split('/\r\n|\r|\n/', $data);
        
        $data = array_map(function($line) {
            $line = explode(' ', $line);
            $hand['cards'] = str_split(trim($line[0]));
            $hand['bid'] = (int) trim($line[1]);
            $hand['strength'] = 0;
            return $hand;
        }, $data);
        return $data;
    }

}