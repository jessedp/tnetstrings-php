tnetstrings-php
===============

Essentially a straight port of Zed Shaw's [tnetstring python reference implementation](http://tnetstrings.org/) to php

_tns_test.php_  provides some basic encode/decode tests. 

known issues
---------------

- currently there is one test failure (prove me wrong, I'm sure there are more) - I'm assuming that my encoded version should match the reference implementation's encoded version even though functionally that probably shouldn't matter. It would seem that in that example the reference implementation may not maintain parameter order, creating my issue.
- in an effort to support the list/dict types, arrays that return the key of their first index as 0 are treated as lists. That most definitely can be _wrong_ .
