<?php

class Timer {

    private $start, $end;
    private $totaltime;

    public function __construct() {
        $this->add();
    }

    public function add() {
        if (!$this->start) {
            $microtime = explode(' ', microtime());
            $this->start = $microtime[1] + $microtime[0];
        }
    }

    public function stop() {
        if ($this->start) {
            $microtime2 = explode(' ', microtime());
            $this->end = $microtime2[1] + $microtime2[0];
            $this->totaltime = $this->end - $this->start;
        }
    }

    public function get() {
        return number_format($this->totaltime, 7);
    }

    public function reset_timer() {
        $start = $end = $totaltime = '';
    }

}

?>