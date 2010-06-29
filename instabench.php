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
        call_user_func_array($func, $args);
        array_push($this->members, array($func, $args));
    }

    public function run() {
        foreach($this->members as $func):
            $this->start();
            for($i = 0; $i < $this->iterations; $i++)
                call_user_func_array($func[0], $func[1]);

            array_push($this->results, $this->stop());
        endforeach;
    }

    public function results() {
        // Are we running this trough a browser?
        $browser = (bool) isset($_SERVER['SERVER_ADDR']);

        // Fetch max width so we can pad align
        array_walk($this->members, array('InstaBench', 'get_max_width'));

        $output = array(
            sprintf("InstaBench %s (PHP %s)\n", self::VERSION, phpversion()),
            sprintf("Benchmarking results (%s iterations)", $this->iterations)
        );
        array_push($output, "===");

        /*
         * Add each result to array with proper padding and a comparision
         * against baseline (first function added)
         */
        $num = count($this->members);
        for($i = 0; $i < $num; $i++):
            $factor = round(($this->results[0] / $this->results[$i]), 1);
            $pad = ($this->width - strlen($this->members[$i][0])) + 1;
            $comparison = ($i == 0 ? "baseline" : sprintf("%.1fx %s",
                $factor,
                $factor < 1 ? "slower" : ($factor === 1.0 ?
                    "equal" : "faster")
            ));

            array_push($output, sprintf("%s%s: %sms %s",
                str_repeat(" ", $pad),
                $this->members[$i][0],
                $this->results[$i],
                $num > 1 ? sprintf("(%s)",$comparison) : ""
            ));
        endfor;

        // Cosmetics, re-use $this->width to figure out new console width
        array_walk($output, array('InstaBench', 'get_max_width'));

        $output[array_search("===", $output)] = str_repeat("=", $this->width);
        array_push($output, "\n");

        if($browser) header('Content-type: text/plain');

        print implode("\n", $output);
    }

    private function get_max_width($row) {
        $length = is_array($row) ? strlen($row[0]) : strlen($row);
        $this->width = max($this->width, $length);
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