<?php
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use frhel\adventofcode2023php\Tools\Timer;

class Day7 extends Command
{
    protected static $day = 7;
    protected static $defaultName = 'Day7';
    protected static $defaultDescription = 'Advent of Code 2023 Solution';
    protected static $bashColors = ['blue' => '\033[0;34m', 'light_blue' => '\033[1;34m', 'green' => '\033[0;32m', 'light_green' => '\033[1;32m', 'cyan' => '\033[0;36m', 'light_cyan' => '\033[1;36m', 'red' => '\033[0;31m', 'light_red' => '\033[1;31m', 'purple' => '\033[0;35m', 'light_purple' => '\033[1;35m', 'brown' => '\033[0;33m', 'yellow' => '\033[1;33m', 'light_gray' => '\033[0;37m', 'white' => '\033[1;37m', 'normal' => '\033[0m', 'bold_white' => '\033[1m', 'bold_light_cyan' => '\033[1;96m', 'bold_light_green' => '\033[1;92m'];
    protected $dataFile;
    protected $exampleFile;
    protected $ex;
    protected $hand_strengths;
    protected $card_strengths;

    function __construct() {
        $this->ex = 0;

        // Set the data files to use based on the day
        // ./data/day_{$day}
        // ./data/day_{$day}.ex
        $this->dataFile = __DIR__ . '/../../data/day_' . self::$day;
        $this->exampleFile = __DIR__ . '/../../data/day_' . self::$day . '.ex';

        // The hand strengths are used to rate the hands.
        // ex.
        // AAAAA -> [51 => 7] -> 5 copies of the most frequent card -> 1 total card types -> rated 7 (5 of a kind)
        // AAA3A -> [42 => 6] -> 4 copies of the most frequent card -> 2 total card types -> rated 6 (4 of a kind)
        // A44AA -> [32 => 5] -> 3 copies of the most frequent card -> 2 total card types -> rated 5 (full house)
        // A434A -> [33 => 4] -> 3 copies of the most frequent card -> 3 total card types -> rated 4 (3 of a kind)
        $this->hand_strengths = [51 => 7, 42 => 6, 32 => 5, 33 => 4, 23 => 3, 24 => 2, 15 => 1];

        // Higher is better
        $this->card_strengths =[
            '2' => 1, '3' => 2, '4' => 3, '5' => 4, '6' => 5, '7' => 6, '8' => 7, '9' => 8, 
            'T' => 9, 'J' => 10, 'Q' => 11, 'K' => 12, 'A' => 13
        ];

        parent::__construct();
    }

    // ----------------------------------------------------------------------------
    // Problem description:
    // Solution by: https://github.com/frhel (Fry)
    // ----------------------------------------------------------------------------
    protected function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        $io->writeln(self::$defaultName . ' - ' . self::$defaultDescription);

        // The test data is so small we may as well just load both files in anyways
        $data_full = file_get_contents($this->dataFile);
        $data_example = file_get_contents($this->exampleFile);
        // ====================================================================== //
        // ============================ Start Solving =========================== //
        // ====================================================================== //
        
        // $this->ex = 1; // Default to example data. Just comment out this line to use the real data.
        $data = $this->parse_input($this->ex === 1 ? $data_example : $data_full);

        // Start the timer
        $overallTimer = new Timer();

        $solution = $this->solve($data);

        // Right answer:247823654
        $this->print_answer($solution[0], 1);

        // Right answer: 245461700
        $this->print_answer($solution[1], 2);
        
        // Stop the timer
        $this->print_time($overallTimer->stop(), 'Overall Time');
        return Command::SUCCESS;
    }

    /**
     * Solves the problem
     * 
     * @param array $data The data to solve
     * @return array The solution to the problem in the form of [part1, part2]
     */
    protected function solve($data) {
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
            $cards = str_split($cards); // Easier to work with as an array
            
            // Counts the number of each card and sorts them from highest to lowest
            $cards_map = array_count_values($cards);            
            arsort($cards_map);

            // Check if any of the cards are jokers
            if ($joker && isset($cards_map['J']) && $cards_map['J'] !== 5) {
                $c_strengths = $this->card_strengths;

                // We have a joker, so we only need its value from here on out
                $joker_count = $cards_map['J'];
                unset($cards_map['J']); // Yeet!

                // Grab all entries with highest identical values
                $first_key = array_key_first($cards_map);
                $highest_values = [];
                foreach ($cards_map as $ckey => $value) {
                    if ($value === $cards_map[$first_key]) {
                        $highest_values[$ckey] = $value;
                    }
                }

                // Sort the highest values by their card strength
                uksort($highest_values, function($a, $b) use ($c_strengths) {
                    return $c_strengths[$b] - $c_strengths[$a];
                });
                
                // Grant the top dog the joker count
                $cards_map[array_key_first($highest_values)] += $joker_count;
                // Sort the cards map again so the highest value is on top
                // because we do that by lowest array index
                arsort($cards_map);
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
                [$a_add, $b_add] = $this->find_stronger_hand($a, $b, $joker);
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
    protected function find_stronger_hand($a, $b, $joker) {
        $c_strengths = $this->card_strengths;

        $a_cards = $a['cards'];
        $b_cards = $b['cards'];

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
            $hand['cards'] = trim($line[0]);
            $hand['bid'] = (int) trim($line[1]);
            $hand['strength'] = 0;
            return $hand;
        }, $data);
        return $data;
    }



    // ----------------------------------------------------------------------------
    // Helper functions -----------------------------------------------------------
    // ----------------------------------------------------------------------------
    /**
     * Prints the answer to the console
     *
     * @param int $solution Solution to the problem
     * @param int $part Which part of the problem is being solved
     * @return void
     * 
     * @example 1 print_answer($solution[0], 1);
     * 
     */
    protected function print_answer($solution, $part) {
        $colors = self::$bashColors;
        echo $colors['blue']." sup".$colors['normal'].PHP_EOL;
    }

    /**
     * Prints the time taken to run a function
     *
     * @param float $time Time taken to run function
     * @param string $message Message to print e.g. 'Time to run function'
     * @return void
     * 
     * @example 1 print_time($time, 'Time to run function');
     * 
     */
    protected function print_time($time, $message) {
        $colors = self::$bashColors;
        printf('%s %s: %s %s %s' . PHP_EOL, $colors['light_cyan'], $message, $colors['light_green'], $time, $colors['normal']);
    }

}