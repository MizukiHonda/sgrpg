<?php

//以下のコメントを外すと実行時エラーが発生した際にエラー内容が表示される
//ini_set('display_errors', 'On');
//ini_set('error_reporting', E_ALL);

define('DEFAULT_LV', 1);
define('DEFAULT_EXP', 1);
define('DEFAULT_MONEY', 3000);

$dsn  = 'mysql:dbname=sgrpg;host=127.0.0.1';  
$user = 'senpai';      
$pw   = 'indocurry';   

$sql1 = 'INSERT INTO User(lv, exp, money) VALUES(:lv, :exp, :money)';
$sql2 = 'SELECT LAST_INSERT_ID() as id';  


try{
  $dbh = new PDO($dsn, $user, $pw);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  //SQL準備
  $sth = $dbh->prepare($sql1);
  $sth->bindValue(':lv',    DEFAULT_LV,    PDO::PARAM_INT);
  $sth->bindValue(':exp',   DEFAULT_EXP,   PDO::PARAM_INT);
  $sth->bindValue(':money', DEFAULT_MONEY, PDO::PARAM_INT);

  //実行
  $sth->execute();

  //SQL準備
  $sth = $dbh->prepare($sql2);

  //実行
  $sth->execute();

  //実行結果から1レコード取ってくる
  $buff = $sth->fetch(PDO::FETCH_ASSOC);
}
catch( PDOException $e ) {
  sendResponse(false, 'Database error: '.$e->getMessage());
  exit(1);
}

// データが0件
if( $buff === false ){
  sendResponse(false, 'Database error: can not fetch LAST_INSERT_ID()');
}
// データを正常に取得
else{
  sendResponse(true, $buff['id']);
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
