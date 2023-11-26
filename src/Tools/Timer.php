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


    public function formatTime(String $time) {
        $time = DateTime::createFromFormat('U.u', number_format($time, 4, '.', ''));
        $time = (float) $time->format('s.u');
        return $time . 's';
    }
}
