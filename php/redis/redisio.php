<?php
/**
 * Redis连接池
 * 基于phpredis<https://github.com/nicolasff/phpredis>扩展封装
 * @author imminh<ming.shu@qq.com>
 * @since 2012-07-01
 */

class RedisIo{

    const timeOut = 3; //设置默认3秒超时

    public $instance = NULL;    //redis实例
    private static $_redisIo = NULL;

    protected $_redisConfig = array();

    private function __construct() {
        //加载配置文件
        $this->_redisConfig = require_once './redisconfig.php';

        if($this->_redisConfig['mode'] == 'single') {
            //Redis单机模式
            try {
                $this->instance = new Redis();
                $this->instance->connect($this->_redisConfig['config']['host'], $this->_redisConfig['config']['port'], self::timeOut);
            } catch (RedisException $e) {
                echo 'Caught Redis exception: ',  $e->getMessage(), "\n";
            }
        } elseif($this->_redisConfig['mode'] == 'distributed') {
            //Redis分布式
            try {
                if(is_array($this->_redisConfig['config']) && count($this->_redisConfig['config'])) {
                    $distributed = array();
                    foreach($this->_redisConfig['config'] as $config) {
                        $distributed[] = $config['host'].':'.$config['port'];
                    }
                }
                $this->instance = new RedisArray($distributed);
            } catch (RedisException $e) {
                echo 'Caught Redis exception: ',  $e->getMessage(), "\n";
            }
        } else {
            throw new Exception("Redis Config Error", 1);
        }
    }

    private function __clone() {}

    public static function getInstance() {
        if(!isset(self::$_redisIo) || is_null(self::$_redisIo)){
            self::$_redisIo = new self();
        }
        return self::$_redisIo;
    }
}
