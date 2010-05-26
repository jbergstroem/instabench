<?php
require_once('instabench.php');

$data = array(
    'item_0' => array(array(1,2,3,4,5,6,7,8,9,0),array(1,2,3,4,5,6,7,8,9,0),array(1,2,3,4,5,6,7,8,9,0),),
    'item_1' => array('a','b','c','d','e','f'),
    'item_2' => array('a','b','c','d','e','f','a','b','c','d','e','f','a','b','c','d','e','f','a','b','c','d','e','f'),
    'item_3' => array(1,2,3,4,5,6,7,8,9,0),
    'item_4' => 'kjasdlkjhasdljfhasldkjhfasd98f70p9asjkfnasdfbas9y7fas',
    'item_5' => '987123458796123976187916329847698761239847',
    'item_6' => array(array(1,2,3,4,5,6,7,8,9,0),array(1,2,3,4,5,6,7,8,9,0),array(1,2,3,4,5,6,7,8,9,0),),
    'item_7' => array(array(array('a','b','c','d','e','f'),array('a','b','c','d','e','f')),array(array(array('a','b','c','d','e','f'),array('a','b','c','d','e','f')),2,3,4,5,6,7,8,9,0),array(array(array('a','b','c','d','e','f'),array('a','b','c','d','e','f')),array(array('a','b','c','d','e','f'),array('a','b','c','d','e','f'))),),
    'item_8' => array(array('a','b',array('a','b','c','d','e','f','a','b','c','d','e','f','a','b','c','d','e','f','a','b','c','d','e','f','b','c','d','e','f','a','b','c','d','e','f','a','b','c','d','e','f','a','b','c','d','e','f','b','c','d','e','f','a','b','c','d','e','f','a','b','c','d','e','f','a','b','c','d','e','f','b','c','d','e','f','a','b','c','d','e','f','a','b','c','d','e','f','a','b','c','d','e','f'),'d','e','f','a',array('a','b','c','d','e','f','a','b','c','d','e','f','a','b','c','d','e','f','a','b','c','d','e','f'),'c','d','e','f','a','b','c','d','e','f','a','b','c','d','e','f'),array('a','b','c','d','e','f','a','b','c','d','e','f','a','b','c','d','e','f','a','b','c','d','e','f')),
    'item_9' => array('a','b','c','d','e','f','a','b','c','d','e','f','a','b','c','d','e','f','a','b','c','d','e','f','b','c','d','e','f','a','b','c','d','e','f','a','b','c','d','e','f','a','b','c','d','e','f','b','c','d','e','f','a','b','c','d','e','f','a','b','c','d','e','f','a','b','c','d','e','f','b','c','d','e','f','a','b','c','d','e','f','a','b','c','d','e','f','a','b','c','d','e','f'),
);

$benchmark = new InstaBench(10000);

$benchmark->add("serialize", array($data));
$benchmark->add("var_export", array($data, true));

if(function_exists("igbinary_serialize"))
    $benchmark->add("igbinary_serialize", array($data));

if(function_exists("bson_encode"))
    $benchmark->add("bson_encode", array($data));

if(function_exists("json_encode"))
    $benchmark->add("json_encode", array($data));

try {
    $benchmark->run();
} catch(InstaBenchException $e) {
    exit(sprintf("Something went wrong: %s", $e->getMessage()));
}

// Everything went ok, lets view the results!
$benchmark->results();
