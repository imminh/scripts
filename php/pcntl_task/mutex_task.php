<?php
/**
 * 单机多进程互斥任务
 *
 * 服务器在*nix环境下，PHP安装pcntl，sysvsem模块
 * @author imminh<ming.shu@qq.com>
 * @since 2011-09-18
 */

set_time_limit(0);

/**
 * 使用方法
 * php-cgi方式调用，第一个参数为task脚本文件名，第二个参数为子进程数，第三个参数为间隔时长
 * @example：php mutex_task.php cron_send_mail.php 3 5
 */
if ($argc > 1) {
	$filename = trim($argv[1]);
	$cron_dir = realpath(dirname(__FILE__));
    if (!file_exists( $cron_dir . DIRECTORY_SEPARATOR . $filename)) {
		exit('脚本文件名错误或不存在!');
	}
} else {
	exit('参数错误!');
}

//最大子进程数
$max_child = intval($argv[2]) > 0 ? intval($argv[2]) : 3;

while (true) {
	//sleep N 秒
	sleep($argv[3]);

	//产生互斥信号量
	$key = ftok(__FILE__, 'a');
	$sem_id = sem_get($key, 1);

	//fork子进程
	$child_process = array();
	$child = 1;
	while ($child <= $max_child) {
		$child_pid = pcntl_fork();
		if ($child_pid == -1) {
			exit("子进程创建失败!\n");
		} elseif ($child_pid) {
			$child_process[$child] = $child_pid;
			$child++;
		} else {
			break;
		}
	}

	if ($child_pid) {
		//父进程
		$status = null;
		foreach ($child_process as $pid) {
			pcntl_waitpid($pid, $status);
		}
	} else {
		//子进程
		sem_acquire($sem_id);
		$msg = exec('/usr/bin/php '. $cron_dir . DIRECTORY_SEPARATOR . $filename);
        echo $msg."\r\n";
		sem_release($sem_id);
		exit ;
	}
}
