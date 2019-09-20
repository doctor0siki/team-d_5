<?php

use Model\Dao\Trade;
use Model\Dao\Restaurant;
use Model\Dao\Testaurant;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 商品一覧を出すコントローラです
 *
 * detail/1 = 1番の商品を
 * detail/13 = 13番の商品を
 * 表示する仕組みになっています。
 *
 * {item_id}の中身は$argsに入ります。
 * 取得する時は、$args["item_id"]で取得できます。
 */
$app->get('/restaurant/detail/{restaurant_id}', function (Request $request, Response $response, $args) {

    $data = [];

    //URLパラメータのitem_idを取得します。
    $restaurant_id = $args["restaurant_id"];

    //アイテムDAOをインスタンス化します。
    $restaurant = new Restaurant($this->db);

    //URLパラメータのitem_id部分を引数として渡し、戻り値をresultに格納します
    $data["restaurant"] = $restaurant->select(array("id" => $restaurant_id), "", "", 1, false);

    // Render index view
    return $this->view->render($response, 'restaurant/detail.twig', $data);

});

// 予約完了処理コントローラ
$app->post('/restaurant/detail/', function (Request $request, Response $response) {

    //POSTされた内容を取得します
    // restran_id、people_num（予約申し込み人数）
    $data = $request->getParsedBody();
    
    //ユーザーDAOをインスタンス化
    $trade = new Trade($this->db);
    $restaurants = new Restaurant($this->db);

    $restaurant = $restaurants->select(array("id" => $data["restaurant_id"]), "", "", 1, false);

    // 予約人数を増やす処理
    $reserved_num = $data["people_num"] + $restaurant["reserve_num"];

    $restaurant = $restaurants->update(array("id" => $data["restaurant_id"], "reserve_num" => $reserved_num));

    
    $data["user_id"] = $this->session["user_info"]["id"];

    //DBに登録をする。戻り値は自動発番されたIDが返ってきます
    $id = $trade->insert($data);

    // 登録完了ページを表示します。
    return $this->view->render($response, 'restaurant/complete.twig', $data);
});

