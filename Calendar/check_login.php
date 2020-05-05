<?php
ini_set("session.cookie_httponly", 1);
session_start();
header("Content-Type: application/json");
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
 if(isset($_SESSION['user_id'])&&$_SESSION['user_id']!=null){
     if (!isset($_SESSION['token'])){
        $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
     }
    echo JSON_encode(array('message'=>'true', 'id'=>$_SESSION['user_id'], 'token'=>$_SESSION['token']));
} else{
    if (!isset($_SESSION['token'])){
        $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
     }
    echo JSON_encode(array('message'=>'false', 'token'=>$_SESSION['token']));
}?>