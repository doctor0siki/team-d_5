<?php

use Model\Dao\Event;
use Slim\Http\Request;
use Slim\Http\Response;

// 商品一覧を出すコントローラです
$app->get('/search/', function (Request $request, Response $response, $args) {

    $data= $request->getQueryParams();
    if(!empty($data["date"])){
      //アイテムDAOをインスタンス化します。
      $event = new Event($this->db);

      //アイテム一覧を取得し、戻り値をresultに格納します
      $data["result"] = $event->getByDate($data["date"]);      
    }
    // Render index view
    return $this->view->render($response, '/search/index.twig', $data);

});
