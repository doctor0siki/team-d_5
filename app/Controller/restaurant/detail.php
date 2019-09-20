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

    //ユーザーの情報
    $data["user_id"] = $this->session["user_info"]["id"];

    //trade情報既にあったらエラーにするよ
    $duplicate_trade = $trade->select(array("restaurant_id" => $data["restaurant_id"], "user_id" => $data["user_id"] ), "", "", 1, false);
    if($duplicate_trade){
        $data['error_message'] = '既に予約済みです。';
        // 詳細ページに戻ります。
//        return $response->withRedirect('/restaurant/detail/'.$data["restaurant_id"]);
        // 登録完了ページを表示します。
        return $this->view->render($response, 'restaurant/detail.twig', $data);
    }

    $restaurant = $restaurants->select(array("id" => $data["restaurant_id"]), "", "", 1, false);

    // 予約人数を増やす処理
    $reserved_num = $data["people_num"] + $restaurant["reserve_num"];

    $restaurant = $restaurants->update(array("id" => $data["restaurant_id"], "reserve_num" => $reserved_num));

    //DBに登録をする。戻り値は自動発番されたIDが返ってきます
    $id = $trade->insert($data);

    // 登録完了ページを表示します。
    return $this->view->render($response, 'restaurant/complete.twig', $data);
});

// キャンセルコントローラ
$app->get('/restaurant/cancel/{trade_id}', function (Request $request, Response $response, $args) {

    $data = [];

    $trade_id = $args["trade_id"];

    $trade = new Trade($this->db);

    $trade->delete($trade_id);

    return $this->view->render($response, 'restaurant/cancel.twig', $data);
});
