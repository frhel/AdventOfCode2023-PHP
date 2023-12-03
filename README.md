# AdventOfCode2023-PHP
My solutions for Advent of Code 2023, in PHP

## Usage
I'm using PHP 8, and Symfony's CLI component to run the code. Use `composer install` to install the dependencies.

To create a new day, use `bash new.sh #`, where `#` is the day number. This will create a new file in `src/Solutions called Day#.php`, and new data files in `data/` as well as add to the include list in `bin/solve`.

To run the code for a day, use `php bin/solve Day#`.
