<?php
/**
 * Created by PhpStorm.
 * User: MXS34
 * Date: 23.01.2018
 * Time: 15:30
 */

namespace app\components;

use Redis;
use yii\base\Component;

use app\sse\SseEvent;

class RedisEventService extends Component
{
    /** @var  $host string */
    public $host;

    /** @var  $port integer */
    public $port;

    /** @var  $password string */
    public $password = null;

    /** @var  $redis Redis */
    public $redis;

    public function init()
    {
        parent::init();

        $this->redis = new Redis();

        if (!$this->redis->connect($this->host, $this->port)) {
            throw new \RedisException("Redis connection is failed");
        }


        if (!empty($this->password)){
            if (!$this->redis->auth($this->password)) {
                throw new \RedisException("Redis auth is failed");
            }
        }
    }


    public function publish ($channel, SseEvent $message) {
        $this->redis->publish($channel, serialize($message));
    }


    public function subscribe ($channels, $callback) {
        if (is_callable($callback)) {
            $this->redis->subscribe($channels, $callback);
            return true;
        }

        return false;
    }
}