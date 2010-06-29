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
    private $mem_usage;
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
        set_error_handler("bail");
        $ret = call_user_func_array($func, $args);
        if(!is_null($ret) && $ret)
            array_push($this->members, array($func, $args));
        restore_error_handler();
    }

    public function run() {
        foreach($this->members as $func):
            $this->mem_usage = memory_get_usage();
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

        // eaiser to write
        $m = & $this->members;
        $r = & $this->results;

        /*
         * Add each result to array with proper padding and a comparision
         * against baseline (first function added). Skip comparison if
         * we only have one member.
         */
        $num = count($m);
        for($i = 0; $i < $num; $i++):
            $factor = @round(($r[0]['time'] / $r[$i]['time']), 1);
            $pad = ($this->width - strlen($m[$i][0])) + 1;
            $comparison = ($i == 0 ? 'baseline' : sprintf("%.1fx %s",
                $factor,
                $factor < 1 ? "slower" : ($factor === 1.0 ?
                    "equal" : "faster")
            ));

            array_push($output, sprintf("%s%s: %sms %s",
                str_repeat(" ", $pad),
                $m[$i][0],
                $r[$i]['time'],
                $num > 1 ? sprintf("(%s)", $comparison) : ""
            ));
        endfor;

        /*
         * Add memory usage. No need to compare, they're
         * easy enough to read.
         */
        array_push($output, "\n");
        array_push($output, sprintf("Memory usage (total: %s)",
            formatBytes(memory_get_usage()))
        );
        array_push($output, "===");

        for($i = 0; $i < $num; $i++):
            $pad = ($this->width - strlen($m[$i][0])) + 1;
            array_push($output, sprintf("%s%s: %s",
                str_repeat(" ", $pad),
                $m[$i][0],
                formatBytes($r[$i]["mem"])
            ));
        endfor;

        // Cosmetics, re-use $this->width to figure out new console width
        array_walk($output, array('InstaBench', 'get_max_width'));

        foreach(array_keys($output, "===") as $key)
            $output[$key] = str_repeat("=", $this->width);
        array_push($output, "\n");

        if($browser) header('Content-type: text/plain');

        print implode("\n", $output);
    }

    // Helper for figuring out width
    private function get_max_width($row) {
        $length = is_array($row) ? strlen($row[0]) : strlen($row);
        $this->width = max($this->width, $length);
    }

    private function start() {
        $this->start = array_sum(explode(' ', microtime()));
    }

    private function stop() {
        $stop = round((array_sum(explode(' ',
            microtime())) - $this->start) * 1000, 0);
        $usage = memory_get_usage() - $this->mem_usage;
        return array("time" => $stop, "mem" => $usage);
    }
}

/*
 * Intercept errors while adding functions to benchmark
 * This aids you in not displaying the error messages $iterations times
 */
function bail($errno, $errstr, $errfile, $errline) {
    $backtrace = debug_backtrace();
    // nice api
    $func = $backtrace[0]["args"][4]["func"];
    printf("Note: error while adding %s, skipping\n", $func);
    return true;
}

// Chris Jester-Young's byteformatter
function formatBytes($size, $precision = 2) {
    $base = log($size) / log(1024);
    $suffixes = array('b', 'k', 'M', 'G', 'T');

    return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
}