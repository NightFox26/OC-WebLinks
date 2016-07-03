<?php

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

// Register global error and exception handlers
ErrorHandler::register();
ExceptionHandler::register();

// Register service providers
$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

// Register services
$app['dao.link'] = $app->share(function ($app) {
    $linkDAO = new WebLinks\DAO\LinkDAO($app['db']);
    return $linkDAO;
});

$app['dao.user'] = $app->share(function ($app) {
    $userDAO = new WebLinks\DAO\UserDAO($app['db']);
    return $userDAO;
});

//var_dump($app['dao.user']->findAll());
//$app['dao.user']->deleteUser(2);
