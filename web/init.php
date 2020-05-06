<?php

$cmd = "php ../yii migrate --interactive=0";

$output = stream_get_contents(popen($cmd, 'r'));

echo $output;

$cmd = "php ../yii rates/daily";
