<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Example\HeavyProcessor;

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

$heavyProcessor = new HeavyProcessor();
$heavyProcessor->receiveQueue();
