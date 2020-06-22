<?php

//以下のコメントを外すと実行時エラーが発生した際にエラー内容が表示される
//ini_set('display_errors', 'On');
//ini_set('error_reporting', E_ALL);

//ユーザーIDを受け取る
$uid = isset($_GET['uid'])?  $_GET['uid']:null;

//Validation
if( ($uid === null) || (!is_numeric($uid)) ){
  sendResponse(false, 'Invalid uid');
  exit(1);
}

$dsn  = 'mysql:dbname=sgrpg;host=127.0.0.1';  
$user = 'senpai';      
$pw   = 'indocurry';   

//実行したいSQL
$sql = 'SELECT * FROM User WHERE id=:id'; 


try{
  $dbh = new PDO($dsn, $user, $pw);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $sth = $dbh->prepare($sql);

  //プレースホルダに値を入れる
  $sth->bindValue(':id', $uid, PDO::PARAM_INT);

  //実行
  $sth->execute();

  //実行結果から1レコード取ってくる
  $buff = $sth->fetch(PDO::FETCH_ASSOC);
}
catch( PDOException $e ) {
  sendResponse(false, 'Database error: '.$e->getMessage());
  exit(1);
}

//データが0件
if( $buff === false ){
  sendResponse(false, 'Not Fund user');
}
//データを正常に取得
else{
  sendResponse(true, $buff);
}

/**
 * 実行結果をJSON形式で返却する
 *
 * @param boolean $status
 * @param array   $value
 * @return void
 */
function sendResponse($status, $value=[]){
  header('Content-type: application/json');
  echo json_encode([
    'status' => $status,
    'result' => $value
  ]);
}
