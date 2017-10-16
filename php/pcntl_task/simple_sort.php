<?php
set_time_limit(0);

$array = array(8,4,10,5,2,1,7,3,6,9);

$child_process = array();
foreach($array as $child){
    $child_pid = pcntl_fork();
    if ($child_pid) {
        $child_process[$child] = $child_pid;
    } else {
        break;
    }
}

if ($child_pid) {
    $status = null;
    foreach ($child_process as $pid) {
        pcntl_waitpid($pid, $status);
    }
} else {
    sleep($child);
    echo $child."\n";
}