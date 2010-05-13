==========
InstaBench
==========

A simple benchmark class that compares function calls to each other, using the
first function as baseline. The result is presented in msecs and a webkit-style
"N times faster/slower" fashion.

This library requires PHP 4.3.0 or greater. It is licensed under a 2 clause BSD
license. Fork away!


Using InstaBench
----------------
Simple example that compares a couple of serialization options in PHP::

  $data = array(1,2,3,4,5,6,7,8,9,0 => array(1,2,3,4,5,6,7,8,9));

  $benchmark = new InstaBench();

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

