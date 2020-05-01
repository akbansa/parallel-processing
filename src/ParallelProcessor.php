<?php

namespace Akbansa\ParallelProcessing;

use Symfony\Component\Process\Process;

/**
 * Class ParallelProcessor
 * @package Akbansa\ParallelProcessing
 */
class ParallelProcessor
{
    private $tasks;
    private $processing = [];
    private $parallelCount = 3;
    private $estimateProcessTime = 1; //in sec

    CONST WAITING = 1;
    CONST PROCESSING = 2;
    CONST COMPLETED = 3;

    /**
     * ParallelProcessor constructor.
     * @param $tasks
     * @param array $options
     */
    public function __construct($tasks, $options = [])
    {
        $this->setup($tasks, $options);
    }

    private function setup($tasks, $options) {

        foreach ($tasks as $task) {
            $task['state'] = self::WAITING;
            $this->tasks[] = $task;
        }

        foreach ($options as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * This is to start the processing
     */
    public function start() {
        $this->process();
        $this->checkAvailability();
    }

    private function process() {
        while (
            (count($this->processing) < $this->parallelCount) &&
            $task = $this->getNextTask())
        {
            $process = new Process($task);
            $process->setTimeout(0);
            $process->disableOutput();
            $process->start();
            $this->processing[] = $process;
        }
    }

    private function checkAvailability() {
        while (count($this->processing) || !$this->isFinished()) {
            foreach ($this->processing as $i => $runningProcess) {
                if (! $runningProcess->isRunning()) {
                    unset($this->processing[$i]);
                    $this->process();
                    $this->taskFinish($i);
                }
            }
            sleep($this->estimateProcessTime);
        }
    }

    /**
     * This function will return if any task is pending for processing and return the command for that, also mark the task in processing
     * returns null if no task is remaining
     * command: the task execution command
     * @return string|null
     */
    private function getNextTask() {
        foreach ($this->tasks as $i => $task) {
            if($task['state'] === self::WAITING) {
                $this->tasks[$i]['state'] = self::PROCESSING;
                return $task['command'];
            }
        }
        return null;
    }

    /**
     * @param $key
     * Mark the task as completed
     */
    private function taskFinish($key) {
        $this->tasks[$key]['state'] = self::COMPLETED;
    }

    /**
     * To check if all the task execution is completed or not
     * return true if all are completed
     * return false if some are pending
     * @return bool
     */
    public function isFinished() {
        foreach ($this->tasks as $task) {
            if($task['state'] !== self::COMPLETED) {
                return false;
            }
        }
        return true;
    }

}

