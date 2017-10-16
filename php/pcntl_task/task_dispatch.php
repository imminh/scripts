<?php
/**
 * 多进程同步任务
 * 
 * 服务器在*nix环境下，PHP安装pcntl，sysvsem模块
 * @author imminh<ming.shu@qq.com>
 * @since 2011-09-18
 */

set_time_limit(0);

include_once 'task_conf.php';

$child_task_process = array();
if (is_array($config['task_list']) && !empty($config['task_list'])) {
    foreach ($config['task_list'] as $task) {
        $proc_exist = false;
        if (trim(exec("ps -ef|grep -E " . $task['path'] . "|grep -v grep")) != '') {
            // 判断进程是否为僵尸进程
            if (exec("ps -ef|grep -E " . $task['path'] . "|grep defunct|grep -v grep|wc -l") > 0) {
                exec("ps -ef|grep -E " . $task['path'] . "|grep defunct|grep -v grep|awk'{print \"kill -9 \"$2, $3}'");
            } else {
                echo "进程 " . $task['path'] . " 已存在\r\n";
                $proc_exist = true;
                continue;
            }
        }

        $child_pid = pcntl_fork();
        if ($child_pid) {
            $child_task_process[] = $child_pid;
        } else {
            break;
        }
    }

    if ($proc_exist == false) {
        if ($child_pid) {
            $status = null;
            foreach ($child_task_process as $pid) {
                pcntl_waitpid($pid, $status, 0);
            }
        } else {
            //文件不存在跳出
            if (!file_exists($task['path']))
                exit(1);

            /**
             * 判断是否为互斥任务
             */
            $cron_dir = realpath(dirname(__FILE__));
            if ($task['mutex']) {
                $cron_script = $cron_dir . DIRECTORY_SEPARATOR . 'mutex_task.php';
            } else {
                $cron_script = $cron_dir . DIRECTORY_SEPARATOR . 'sync_task.php';
            }

            $args = array($cron_script, $task['path'], $task['child'], $task['time']);
            pcntl_exec('/usr/bin/php', $args);
            exit ;
        }
    }
}
