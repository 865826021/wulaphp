<?php
namespace wulaphp\util;

class RedisClient {

    public static function getRedis($cnf, $database = 0, $prefix = '') {
        if (is_string ( $cnf )) {
            $cnf = array (
                $cnf,6379
            );
        }
        if (count ( $cnf ) == 1) {
            $cnf [1] = 6379;
        }
        $redis = new Redis ();
        if (count ( $cnf ) > 2) {
            $rst = $redis->connect ( $cnf [0], $cnf [1], $cnf [2] );
        } else {
            $rst = $redis->connect ( $cnf [0], $cnf [1] );
        }
        if ($rst) {
            $redis->select ( $database );
            $redis->setOption ( Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP );
            if ($prefix) {
                $redis->setOption ( Redis::OPT_PREFIX, $prefix . ':' );
            }
            return $redis;
        } else {
            return null;
        }
    }
}