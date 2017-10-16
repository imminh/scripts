<?php
/**
 * Redis连接池配置文件
 * 基于phpredis<https://github.com/nicolasff/phpredis>扩展封装
 * @author imminh<ming.shu@qq.com>
 * @since 2012-07-01
 */

//单机模式
$redisConfig = array(
    'mode' => 'single',
    'config' => array(
        'host' => '127.0.0.1',
        'port' => '6379'
    )
);

//分布式(一致性hash)
$redisConfig = array(
    'mode' => 'distributed',
    'config' => array(
        array(
            'host' => '127.0.0.1',
            'port' => '6380'
        ),
        array(
            'host' => '127.0.0.1',
            'port' => '6381'
        ),
        array(
            'host' => '127.0.0.1',
            'port' => '6382'
        ),
    )
);

/**
 * @todo 主从模式
 */

return $redisConfig;
