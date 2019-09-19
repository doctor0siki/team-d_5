<?php

use Model\Dao\Restaurant;
use Slim\Http\Request;
use Slim\Http\Response;

// 商品一覧を出すコントローラです
$app->get('/restaurant/list/{event_id}', function (Request $request, Response $response, $args) {

    $data=[];

    //アイテムDAOをインスタンス化します。
    $restaurant = new Restaurant($this->db);

    //URLパラメータのitem_idを取得します。
    $event_id = $args["event_id"];

    //アイテム一覧を取得し、戻り値をresultに格納します
    $data["restaurants"] = $restaurant->select(array("event_id" => $event_id), "", "", 10, true);

    // Render index view
    return $this->view->render($response, 'restaurant/list.twig', $data);

});
