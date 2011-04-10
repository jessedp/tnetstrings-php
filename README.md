tnetstrings-php
===============

Essentially a straight port of Zed Shaw's [tnetstring python reference implementation](http://tnetstrings.org/) to php

_tns_test.php_ does provide some basic encode/decode tests. There is currently one failure as I'm assuming that my encoded version should match the reference implementation's encoded version even though functionally that probably shouldn't matter. It would seem that in that example the reference implementation may not maintain parameter order, causing the issue.
