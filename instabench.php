<?php
/*
 * A simple class for benchmarking functions
 * Yes, its sad that results() take up most of the code.
 */
class InstaBench {
    const VERSION = '0.1';

    private $members = array();
    private $results = array();
    private $start;
    private $lap;
    private $width = 0;

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
        $output = array(
          sprintf("InstaBench %s (PHP %s)\n", self::VERSION, phpversion()),
          sprintf("Benchmarking results (%s iterations)", $this->iterations),
        );
        array_push($output, str_repeat("=", strlen($output[1])));

        // Fetch max width so we can pad align
        array_walk($this->members, array('InstaBench', 'get_max_width'));

        /*
         * Add each result to array with proper padding and a comparision
         * against baseline (first function added)
         */
        for($i=0; $i < count($this->members); $i++):
            $factor = round(($this->results[0] / $this->results[$i]), 1);
            $pad = 2 + ($this->width - strlen($this->members[$i][0]));
            array_push($output, sprintf("%s%s: %sms (%s)",
                str_repeat(" ", $pad),
                $this->members[$i][0],
                $this->results[$i],
                $i == 0 ? "baseline" : sprintf("%.1fx %s",
                    $factor,
                    $factor < 1 ? "slower" : ($factor === 1.0 ?
                        "equal" : "faster")
                )
            ));
        endfor;
        // Cosmetic nit
        array_push($output, "\n");

        print implode("\n", $output);
    }

    private function get_max_width($row) {
        $length = strlen($row[0]);
        $this->width < $length ? $this->width = $length : null;
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