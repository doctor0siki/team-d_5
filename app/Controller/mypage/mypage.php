<?php

use Model\Dao\Trade;
use Model\Dao\Event;
use Model\Dao\Restaurant;
use Slim\Http\Request;
use Slim\Http\Response;

// TOPページのコントローラ
$app->get('/mypage', function (Request $request, Response $response) {

    $data = [];
    $data["reserves"] = [];

    //アイテムDAOをインスタンス化します。
    $event = new Event($this->db);
    $trade = new Trade($this->db);
    $restaurant = new Restaurant($this->db);

    $user_id = $this->session["user_info"]["id"];

    // 予約状況を検索
    $trade_datas = $trade->select(array("user_id" => $user_id), "", "", 10, true);
    $num = 0;
    foreach($trade_datas as $trade_data){
        $num++;
        $data["reserves"][$num] = $restaurant->select(array("id" => $trade_data["restaurant_id"]), "", "", 10, false);
        
        $data["reserves"][$num]["people_num"] = $trade_data["people_num"];
    }

    //アイテム一覧を取得し、戻り値をresultに格納します
    $data["events"] = $event->getEventList();
    

    // dd($data);

    // Render index view
    return $this->view->render($response, 'mypage/index.twig', $data);
});
