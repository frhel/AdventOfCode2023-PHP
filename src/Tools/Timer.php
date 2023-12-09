<?php

namespace frhel\adventofcode2023php\Tools;

use DateTime;

class Timer {
    private String $startTime;
    private String $endTime;
    private array $checkpoints;

    function __construct() {
        $this->checkpoints = [];
        $this->start();

    }

    public function reset() {
        $this->startTime = null;
        $this->endTime = null;
        $this->checkpoints = [];
    }
    public function start() {
        $this->startTime = microtime(true);
    }
    public function stop() {
        $this->endTime = microtime(true);
        return $this->getElapsedTime();
    }
    

    public function getStartTime() {
        return $this->startTime;
    }
    public function getElapsedTime() {
        return $this->formatTime($this->endTime - $this->startTime);
    }
    public function getStopTime() {
        return $this->endTime;
    }


    public function checkpoint(String $name = null) {
        if ($name) {
            $this->checkpoints[$name] = microtime(true);
        } else {
            $this->checkpoints[] = microtime(true);
        }        
    }
    public function getCheckpoints() {
        return $this->checkpoints;
    }
    public function getLastCheckpoint() {
        return $this->checkpoints[count($this->checkpoints) - 1];
    }
    public function getCheckpoint($index) {
        return $this->checkpoints[$index];
    }
    public function avg_time() {
        $total = array_sum($this->calc_elapsed_times($this->checkpoints));
        return $this->formatTime($total / count($this->checkpoints));
    }

    public function median_time() {
        $times = $this->calc_elapsed_times($this->checkpoints);
        return $this->formatTime($times[~~(count($times)) / 2]);

    }

    private function calc_elapsed_times($arr) {
        $values = [];
        $last = $this->startTime;
        foreach ($this->checkpoints as $checkpoint) {
            $values[] = $checkpoint - $last;
            $last = $checkpoint;
        }
        return $values;
    }


    public function formatTime(String $time) {
        $time = DateTime::createFromFormat('U.u', number_format($time, 4, '.', ''));
        $time = (float) $time->format('s.u');
        return $time . 's';
    }
}
