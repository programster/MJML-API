<?php

require_once(__DIR__ . '/../bootstrap.php');



$app = Slim\Factory\AppFactory::create();
$app->addErrorMiddleware(displayErrorDetails:  false, logErrors: true, logErrorDetails: true);
HomeController::registerRoutes($app);
$app->run();

