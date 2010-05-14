<?php
require_once('instabench.php');

$data = array(1,2,3,4,5,6,7,8,9,0 => array(1,2,3,4,5,6,7,8,9));

$benchmark = new InstaBench(100000);

$benchmark->add("serialize", array($data));
$benchmark->add("json_encode", array($data));
$benchmark->add("var_export", array($data, true));

try {
    $benchmark->run();
} catch(InstaBenchException $e) {
    printf("Something went wrong: %s", $e->getMessage());
}

// Everything went ok, lets view the results!
$benchmark->results();
