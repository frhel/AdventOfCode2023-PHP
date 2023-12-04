<?php
declare(strict_types=1);

namespace frhel\adventofcode2023php\Solutions;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use frhel\adventofcode2023php\Tools\Timer;



class Day4 extends Command
{
    protected static $day = 4;
    protected static $defaultName = 'Day4';
    protected static $defaultDescription = 'Advent of Code 2023 Solution';
    protected $dataFile;
    protected $exampleFile;
    protected $ex;

    function __construct() {
        $this->ex = 0;

        $this->dataFile = __DIR__ . '/../../data/day_' . self::$day;
        $this->exampleFile = __DIR__ . '/../../data/day_' . self::$day . '.ex';

        parent::__construct();
    }

    // ----------------------------------------------------------------------------
    // Problem description: https://adventofcode.com/2023/day/04
    // Solution by: https://github.com/frhel (Fry)
    // ----------------------------------------------------------------------------
    protected function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        $io->writeln(self::$defaultName . ' - ' . self::$defaultDescription);

        // The test data is so small we may as well just load both files in anyways
        $data = file_get_contents($this->dataFile);
        $data_example = file_get_contents($this->exampleFile);

        // $this->ex = 1;
        $data = $this->parse_input($this->ex === 1 ? $data_example : $data);
        
        // ====================================================================== //
        // ============================ Start Solving =========================== //
        // ====================================================================== //
        // Start the timer
        $overallTimer = new Timer();
        $solution = $this->solve($data);

        // Right answer: 15205
        $io->success('Part 1 Solution: ' .  $solution[0]);

        // Right answer: 6189740
        $io->success('Part 2 Solution: ' .  $solution[1]);

        $io->writeln('Total time: ' . $overallTimer->stop());  

        return Command::SUCCESS;
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
    protected function solve($cards) {
        $point_sum = 0;
        $total_cards = 0;

        foreach ($cards as $key => $card) {
            $card = $cards[$key];
            $winners = $cards[$key]['winners'];
            $numbers = $cards[$key]['numbers'];

            // Count the wins for this card once
            $win_count = count(array_intersect($winners, $numbers));
            
            // If the card has more than one win, it's worth 2^(wins-1) points
            // otherwise it's worth $win_count points (1 or 0)
            $points = $win_count > 1 ? pow(2, $win_count - 1) : $win_count;
            
            // For every copy of this card, add the number of wins to the count of the next $j cards
            // That way we keep track of how many cards are in the game as we go
            for ($j = 1; $j <= $win_count; $j++) {
                $next_key = $key + $j;
                if ($next_key >= count($cards)) { break; } // We're at the end of the game, stop counting
                $cards[$next_key]['count'] += $card['count'];
            }

            // Add the points for this card to the total sum of points
            $point_sum += $points; 
            // Add the number of copies of this card to the total number of cards
            $total_cards += $card['count'];
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
            $data_out[$key]['winners'] = explode(' ', $card[0]);
            $data_out[$key]['numbers'] = explode(' ', $card[1]);

            // Saving the number of copies of this card for later
            $data_out[$key]['count'] = 1;

            // Convert the numbers to integers and trim whitespace
            foreach ($data_out[$key]['numbers'] as $idx => $number) {
                if ($number === '' || $number === ' ') {
                    unset($data_out[$key]['numbers'][$idx]);
                    continue;
                }
                $data_out[$key]['numbers'][$idx] = (int) trim($number);
            }
            foreach ($data_out[$key]['winners'] as $idx => $winner) {
                if ($winner === '' || $winner === ' ') {
                    unset($data_out[$key]['winners'][$idx]);
                    continue;
                }
                $data_out[$key]['winners'][$idx] = (int) trim($winner);
            }

        }
        return $data_out;
    }

}