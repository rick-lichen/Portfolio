<?php
    ini_set("session.cookie_httponly", 1);
    session_start();
    require 'database.php';
    header("Content-Type: application/json");
    $json_str = file_get_contents('php://input');
    $json_obj = json_decode($json_str, true);
    if(!hash_equals($_SESSION['token'], $json_obj['token'])){
        die("Request forgery detected");
    }
    $share_user = (string)$json_obj['user'];
    $stmt = $mysqli->prepare("SELECT id, username FROM users");
    if (!$stmt){
        echo JSON_encode($stmt->error);
        exit;
    }
    $stmt->execute();
    $stmt->bind_result($return_id,$return_username);
    while($stmt->fetch()){
        if ($return_username==$share_user){
            echo JSON_encode(array("id"=>$return_id));
            exit;
        }
    }
    echo JSON_encode(array("id"=>null));
    exit;
        // if ($returned_id==null){
        //     echo JSON_encode($share_user);
        //     exit;
        // }
        //exit;
        // else{
        //     echo JSON_encode($stmt->error);
        //     exit;
        // }
    
?>