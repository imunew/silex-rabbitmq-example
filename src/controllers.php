<?php

use App\Example\HeavyProcessor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/* @var $app \Silex\Application */

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html.twig', array());
})
;

$app->get('/example', function (Request $request) use ($app) {

    $result = $request->get('result', []);
    $hash = $request->get('hash', md5(time()));
    $startTime = $request->get('startTime', '');
    $endTime = $request->get('endTime', '');

    return $app['twig']->render('app/example.html.twig', [
        'result' => $result,
        'hash' => $hash,
        'startTime' => $startTime,
        'endTime' => $endTime,
    ]);
})
;

$app->post('/example', function (Request $request) use ($app) {

    $executeAsync = $request->get('execute-async', 'false');
    $startTime = date('Y-m-d H:i:s');

    $heavyProcessor = new HeavyProcessor();
    $result = [];
    $async = ($executeAsync === 'true');
    $callback = null;
    if (!$async) {
        $callback = function($number) use (&$result){
            $result[] = "#{$number} is done.";
        };
    }
    $hash = $request->get('hash');
    $heavyProcessor->execute($async, $callback, ['message_id' => $hash]);
    $endTime = $async ? '': date('Y-m-d H:i:s');

    $parameters = [
        'result' => $result, 'hash' => $hash,
        'startTime' => $startTime, 'endTime' => $endTime
    ];
    $subRequest = Request::create('/example', Request::METHOD_GET, $parameters);
    $response = $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
    return $response;
})
;

$app->get('/listen/{hash}', function (Request $request, $hash) use ($app) {

    $result = [];
    $heavyProcessor = new HeavyProcessor();
    $json = $heavyProcessor->checkResult($hash);
    if (!empty($json)) {
        $result = [
            'message' => json_decode($json, true),
            'endTime' => date('Y-m-d H:i:s')
        ];
    }

    return new JsonResponse($result);
})
;



$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html',
        'errors/'.substr($code, 0, 2).'x.html',
        'errors/'.substr($code, 0, 1).'xx.html',
        'errors/default.html',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
