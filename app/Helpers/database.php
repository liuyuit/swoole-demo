<?php


if (!function_exists('getRedisClient')) {
    /**
     * @return Redis
     */
    function getRedisClient(): Redis
    {
        $redis = new \Redis();
        $redis->connect($_ENV['REDIS_IP'], $_ENV['REDIS_PORT']);
        $redis->auth($_ENV['REDIS_PASSWORD']);
        $redis->select($_ENV['REDIS_DB']);
        return $redis;
    }
}

if (!function_exists('remember')) {
    /**
     * 缓存数据
     *
     * @param $uniqId
     * @param $dataSource mixed|\Closure 数据来源
     * @param int $ttl
     * @return mixed|string
     */
    function remember($uniqId, $dataSource, int $ttl = 60)
    {
        $redisKey = 'remember:' . $uniqId;
        $redis = getRedisClient();
        $result = $redis->get($redisKey);

        if ($result) {
            return unserialize($result);
        }

        if ($dataSource instanceof \Closure) {
            $result = $dataSource();
        } elseif (is_array($dataSource) && isset($dataSource[0]) && is_object($dataSource[0])) {
            $object = $dataSource[0];
            $function = $dataSource[1];
            $args = $dataSource[2] ?? [];
            $result = call_user_func_array([$object, $function], $args);
        } else {
            $result = $dataSource;
        }

        $redis->setex($redisKey, $ttl, serialize($result));
        return $result;
    }
}

if (!function_exists('forget')) {
    /**
     * 清除已缓存的数据
     *
     * @param $uniqId
     * @return int
     */
    function forget($uniqId): int
    {
        $redisKey = 'remember:' . $uniqId;
        $redis = getRedisClient();
        return $redis->del($redisKey);
    }
}
