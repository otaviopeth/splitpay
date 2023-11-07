<?php

use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/../vendor/autoload.php';
require '../src/cfg/db.php';

$app = AppFactory::create();

// Parse json, form data and xml
$app->addBodyParsingMiddleware();

// Add Slim routing middleware
$app->addRoutingMiddleware();

// Set the base path to run the app in a subdirectory.
// This path is used in urlFor().
$app->add(new BasePathMiddleware($app));

$app->addErrorMiddleware(true, true, true);


// Rotas para os produtos

require '../src/routes/produtos.php';


// Run app
$app->run();