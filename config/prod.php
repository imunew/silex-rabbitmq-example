<?php

use Monolog\Logger;
use Silex\Provider\MonologServiceProvider;

// configure your app for the production environment

// Global settings
setlocale(LC_ALL, 'ja_JP.UTF-8');
mb_language('Japanese');
mb_internal_encoding('UTF-8');
date_default_timezone_set('Asia/Tokyo');

$app['twig.path'] = array(__DIR__.'/../templates');
$app['twig.options'] = array('cache' => __DIR__.'/../var/cache/twig');

$app->register(new MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../var/logs/app.log',
    'monolog.level' => Logger::NOTICE
));
