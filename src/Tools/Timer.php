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
        $this->startTime = microtime();
    }
    public function stop() {
        $this->endTime = microtime();
        return $this->getElapsedTime();
    }
    

    public function getStartTime() {
        return $this->startTime;
    }
    public function getElapsedTime() {
        return $this->formatTime($this->startTime, $this->endTime);
    }
    public function getStopTime() {
        return $this->endTime;
    }


    public function checkpoint(String $name = null) {
        if ($name) {
            $this->checkpoints[$name] = microtime();
        } else {
            $this->checkpoints[] = microtime();
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
        $total = $this->calc_elapsed_times($this->checkpoints);
        $total_avg = 0;
        foreach ($total as $t) {
            [$micro, $sec] = explode(' ', $t);
            $total_avg += $micro + $sec;
        }
        $total_avg = $total_avg / count($total);
        [$sec, $micro] = explode('.', $total_avg);
        $micro = '0.'.$micro;
        $total_avg = $micro.' '.$sec;        
        return $this->formatTime($total_avg);
    }

    public function median_time() {
        $times = $this->calc_elapsed_times($this->checkpoints);
        return $this->formatTime($times[~~(count($times)) / 2]);
    }

    private function calc_elapsed_times($arr) {
        $values = [];
        $last = $this->startTime;
        foreach ($this->checkpoints as $c) {
            [$sec, $micro] = explode('.', $this->format_micro($last, $c));
            $micro = '0.'.$micro;
            $values[] = $micro.' '.$sec;
            $last = $c;
        }
        return $values;
    }

    public function formatTime(String $start, String $end = null) {
        $time = (float) $this->format_micro($start, $end);
        if ($time < 0.001) {
            $time = $this->formatMicroseconds($time);
        } else if ($time < 1) {
            $time = $this->formatMilliseconds($time);
        } else {
            $time = $this->formatSeconds($time);
        }
        return $time;
    }
    
    public function format_micro($start, $end = null) {
        $start = number_format(array_sum(explode(' ', $start)), 8, '.', '');
        if ($end !== null) {
            $end = number_format(array_sum(explode(' ', $end)), 8, '.', '');
            $start = $end - $start;
        }
        return $start;
    }

    public function formatMilliseconds(String $time) {
        $time = $time * 1000;
        $time = number_format($time, 2, '.', '');
        return $time. 'ms';
    }

    public function formatMicroseconds(String $time) {
        $time = $time * 1000000;
        $time = number_format($time, 0, '.', '');
        return $time. 'µs';
    }

    public function formatSeconds(String $time) {
        $time = number_format($time, 2, '.', '');
        return $time. 's';
    }   
}
