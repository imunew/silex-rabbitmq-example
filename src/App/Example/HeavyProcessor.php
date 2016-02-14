<?php

namespace App\Example;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Redis;


class HeavyProcessor {

    /** @var  Redis */
    private $redis;

    public function __construct()
    {
        $this->redis = new Redis();
        $this->redis->connect('localhost');
    }

    public function execute($async, $callback = null, $parameters = []) {

        if ($async) {
            $this->sendQueue($parameters);
            return;
        }

        if (!is_callable($callback)) {
            return;
        }

        foreach (range(1, 10) as $number) {

            $callback($number);
            usleep(1000000);
        }
    }

    private function sendQueue($parameters = []) {

        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $messageId = isset($parameters['message_id']) ? $parameters['message_id'] : '';

        $channel->queue_declare('execute-heavy-process', false, false, false, false);
        $channel->basic_publish(new AMQPMessage($messageId), '', 'execute-heavy-process');

        $channel->close();
        $connection->close();
    }

    public function receiveQueue() {

        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare('execute-heavy-process', false, false, false, false);

        $callback = function($message) {
            $result = [];
            $heavyProcessor = new HeavyProcessor();
            $heavyProcessor->execute(false, function($number) use (&$result) {
                $result[] = "#{$number} is done.";
            });
            /* @var $message AMQPMessage */
            $this->redis->set($message->body, json_encode($result));
        };

        $channel->basic_consume('execute-heavy-process', '', false, true, false, false, $callback);

        while(count($channel->callbacks)) {
            $channel->wait();
        }
    }

    public function checkResult($hash) {

        $json = $this->redis->get($hash);
        if (!empty($json)) {
            $this->redis->delete($hash);
        }
        return $json;
    }

}