<?php
/**
 * 计划任务配置数组
 * 
 * @author imminh<ming.shu@qq.com>
 * @since 2011-09-18
 * @example
 * $config['task_list'] = array(
 *     array(
 *         'path' => '/path/filename', //脚本路径
 *         'mutex' => true/false,      //是否互斥
 *         'time' => 60,               //任务执行间隔秒数s
 *         'child' => 2                //子进程数
 *     )
 *     ...
 * );
 */
$config['task_list'] = array(
    array(
        'path'  => 'task1.php', 
        'mutex' => false, 
        'time'  => 2, 
        'child' => 3
    ),
    array(
        'path'  => 'task2.php', 
        'mutex' => true, 
        'time'  => 2, 
        'child' => 5
    )
);