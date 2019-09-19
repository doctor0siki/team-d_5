<?php

use Model\Dao\Event;
use Slim\Http\Request;
use Slim\Http\Response;

// TOPページのコントローラ
$app->get('/', function (Request $request, Response $response) {

    $data = [];

    //アイテムDAOをインスタンス化します。
    $event = new Event($this->db);

    //アイテム一覧を取得し、戻り値をresultに格納します
    $data["events"] = $event->getEventList();

    // Render index view
    return $this->view->render($response, 'top/index.twig', $data);
});

// TOPページのコントローラ
$app->get('/sample', function (Request $request, Response $response) {

    $data = [];

    // Render index view
    return $this->view->render($response, 'sample/index.twig', $data);
});

