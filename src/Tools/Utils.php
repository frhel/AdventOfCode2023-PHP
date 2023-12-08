<?php

namespace frhel\adventofcode2023php\Tools;

class Utils {
    /**
     * Calculates the Least Common Multiple of all the numbers in the array
     * @depends gcd
     * 
     * @param array $arr The array of numbers
     * @return int The Least Common Multiple of all the numbers in the array
     */
    public function lcm($arr) {
        // Reduce the array by multiplying all the numbers together and dividing by the Greatest Common Divisor
        // of the current number and the accumulator
        return array_reduce($arr, fn($carry, $item) => ($carry * $item) / $this->gcd($carry, $item), 1);
    }

    /**
     * Calculates the Greatest Common Divisor of two numbers
     * 
     * @param int $a First number
     * @param int $b Second number
     * @return int The Greatest Common Divisor of the two numbers
     */
    public function gcd($a, $b) {
        // Make sure $a is the smaller number
        [$a, $b] = $a > $b ? [$b, $a] : [$a, $b];
        
        // Literally just loop over all the numbers from 2 to $b and check if they divide into both $a and $b
        for ($gcd = 2; $gcd <= $b; $gcd++) {
            if ($a % $gcd === 0 && $b % $gcd === 0) return $gcd;
        }
        return 1;
    }
}