<?php

class InstaBench {
    const VERSION = '0.1';
    
    private $members = array();
    private $results = array();
    private $start;
    private $lap;
    
    public $iterations = 0;
    
    public function __construct($iterations = 10000) {
        $this->iterations = $iterations;
    }
    
    /*
     * Adds function call to stack and runs it once to ensure it actually works
     * Note: Read up on how to format input: php.net/call_user_func_array
     */
    public function add($func, $args) {
        try {
            if(!@call_user_func_array($func, $args))
                throw new InstaBenchException(
                    sprintf("couldn't execute the function '%s'", $func));
        } catch(InstaBenchException $e) {
            exit(sprintf("Something went wrong, %s", $e->getMessage()));
        }
        array_push($this->members, array($func, $args));
    }

    public function run() {
        foreach($this->members as $func):
            $this->start();
            for($i = 0; $i < $this->iterations; $i++):
                call_user_func_array($func[0], $func[1]);
            endfor;
            array_push($this->results, $this->stop());
        endforeach;
    }
    
    public function results() {
        /* TODO */
    }
    
    private function start() {
        $this->start = array_sum(explode(' ', microtime()));
    }
    
    private function stop() {
        $this->lap = round((array_sum(explode(' ',
            microtime()))-$this->start)*1000, 0);
        return $this->lap;
    }
}

class InstaBenchException extends Exception {}