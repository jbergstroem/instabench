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
  $benchmark->add("var_export", array($data, true));

  $benchmark->run();
  $benchmark->results();


Example output
--------------
Here's an example from running the bundled test.php on a test setup::

  % instabench # php test.php
  InstaBench 0.1 (PHP 5.2.13-pl0-gentoo)

  Benchmarking results (10000 iterations)
  =========================================
            serialize: 1054ms (baseline)
           var_export: 2191ms (0.5x slower)
   igbinary_serialize: 482ms (2.2x faster)
          bson_encode: 385ms (2.7x faster)
          json_encode: 447ms (2.4x faster)


  Memory usage (total: 322.51k)
  =========================================
            serialize: 728b
           var_export: 136b
   igbinary_serialize: 136b
          bson_encode: 136b
          json_encode: 136b