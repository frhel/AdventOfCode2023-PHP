<?php

namespace frhel\adventofcode2023php\Tools;

use Amp\Cancellation;
use Amp\Parallel\Worker\Task;
use Amp\Sync\Channel;

class RunTask implements Task {
    public function __construct(
        private array $data,
        private int $part,
        private int $process,
        private int $total_numbers
    ) {}

    public function run(Channel $channel, Cancellation $cancellation): mixed {
        if ($this->part === 1) {
            
            $part = INF;
            foreach($this->data[0] as $seed) {           
                $stuff = $this->find_map_locations($seed, $this->data[1], 0, 0);
                $part = min($part, $stuff);
            }
            return $part;
        } else {
            $part2 = INF;
            $range = $this->data[0];
            $range_elapsed = 0;
            $total_numbers = $this->total_numbers;
            $process = $this->process;
            $total_time = 0;
            $start_time = microtime(true);
            $total_process_numbers = $range[1] - $range[0];
            for ($i = $range[0]; $i <= $range[1]; $i++) {
                $part2 = min($part2, $this->find_map_locations($i, $this->data[1], 0, 0));
                if ($i % 1000000 === 0) {
                    $range_elapsed += 1000000;
                    $percent = round($range_elapsed / $total_process_numbers * 100, 2);
                    $total_time += microtime(true) - $start_time;
                    echo "Process $process: $percent% complete. // " . $range_elapsed . " of " . $total_process_numbers  . ", Process $process: Average time per million: " . round($total_time / $range_elapsed * 1000000, 2) . " seconds\n";
                    
                }
            }
            return $part2;
        }
    }
    
    private function find_map_locations($seed, $maps, $map_key, $map_line): mixed {
        if ($map_key >= count($maps)) return null;

        while (isset($maps[$map_key]) && $map_line < count($maps[$map_key])) {
        // while (isset($maps[$map_key]) && $map_line === 1) {
            $source = $maps[$map_key][$map_line][1];
            $destination = $maps[$map_key][$map_line][0];
            $length = $maps[$map_key][$map_line][2];

            $mapped_val = $this->find_destination($seed, $destination, $source, $length);

            if ($mapped_val !== $seed) {
                return $this->find_map_locations($mapped_val, $maps, $map_key + 1, 0) ?? $mapped_val;
            }
            
            $map_line++;
        }

        return $this->find_map_locations($seed, $maps, $map_key + 1, 0);       
    }

    protected function find_destination($seed, $destination, $source, $length) {
        $mapped_val = $seed;
        if ($seed <= $source + $length && $seed >= $source ) {
            // We are in the range of this map
            // We need to find the distance from source to destination
            $distance = $source - $destination;
            $mapped_val = $seed - $distance;
        }

        return $mapped_val;
    }
}