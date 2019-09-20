<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Model\Dao\User;
use Model\Dao\Restaurant;
use Model\Dao\Trade;


// 会員登録ページコントローラ
$app->get('/resign/', function (Request $request, Response $response) {

    //GETされた内容を取得します。
    $data = $request->getQueryParams();

    // Render index view
    return $this->view->render($response, 'resign/resign.twig', $data);

});

// 会員登録処理コントローラ
$app->post('/resign/', function (Request $request, Response $response) {

    //POSTされた内容を取得します
    $data = $request->getParsedBody();

    //ユーザーDAOをインスタンス化
    $user = new User($this->db);
    $tradeDAO = new Trade($this->db);
    $restrantDAO = new Restaurant($this->db);

    //予約情報を削除（restrantテーブルの予約数を修正するために人数取得しておく
    $trade_list = $tradeDAO->select(array('user_id' => $this->session->user_info["id"]),null,null,null,true );
    //restrant側の予約情報を削除するよ
    foreach($trade_list as $trade){
        $restrantDAO->addReserve($trade['restaurant_id'] ,intval($trade['people_num']) * -1);
    }
    //予約情報を削除するよ
    $tradeDAO->deleteByUser($this->session->user_info["id"]);
    //ログイン中のユーザーを削除する
    $user->delete($this->session->user_info["id"]);

    //セッションから情報削除
    $this->session::destroy();

    // 登録完了ページを表示します。
    return $this->view->render($response, 'resign/resign_done.twig', $data);

});
