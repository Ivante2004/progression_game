<?php

require '../vendor/autoload.php';

use Slim\Factory\AppFactory;

use Ivante2004\Progression\Controller;
use Ivante2004\Progression\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


$db = new Database('../db/games.db');
$controller = new Controller($db);

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response) {
    $html = file_get_contents(__DIR__ . '/index.html');
    $response->getBody()->write($html);
    return $response->withHeader('Content-Type', 'text/html');
});


$app->get('/games', function (Request $request, Response $response) use ($controller) {
    $games = $controller->showGameHistory();
    $response->getBody()->write(json_encode($games));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/games/{id}', function (Request $request, Response $response, array $args) use ($controller) {
    $gameId = (int)$args['id'];
    $game = $controller->showGameHistory($gameId);
    $response->getBody()->write(json_encode($game));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/games', function (Request $request, Response $response)  use ($controller) {
    $data = json_decode($request->getBody());
    $playerName = $data->player_name;

    $games = $controller->playRound($playerName);
    $response->getBody()->write(json_encode($games));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/step/{id}', function (Request $request, Response $response, array $args) use ($controller) {
    $data = json_decode($request->getBody());
    $gameId = (int)$args['id'];
    $answered = $data->answered;

    $games = $controller->checkAnswer($gameId, $answered);
    $response->getBody()->write(json_encode($games));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();

