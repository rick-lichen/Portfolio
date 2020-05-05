<?php
    require 'database.php';
    ini_set("session.cookie_httponly", 1);
    session_start();
    $same = false;
    $json_str = file_get_contents('php://input');
    $json_obj = json_decode($json_str, true);
    $user_id = $json_obj['current_id'];
    $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?"); //checks if the id that is passed in matches the id associated with the session username to prevent abuse of functionality
    $stmt->bind_param('s',$_SESSION['username']);
    $stmt->execute();
    $stmt->bind_result($check_id);
    while($stmt->fetch()){
        if ($user_id==$check_id){
            $same = true;
        }
        else{
            echo json_encode("Abuse of functionality detected!");
            exit;
        }
    }
    $stmt->close();
    if ($same){
        $_SESSION['user_id']=null;
        $_SESSION['username']=null;
        $_SESSION['token']=null;
        echo json_encode("logged out");
        exit;
    }
?>