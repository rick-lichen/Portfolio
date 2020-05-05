<?php
    ini_set("session.cookie_httponly", 1);
    session_start();
    $same =false;
    require 'database.php';
    $json_str = file_get_contents('php://input');
    $json_obj = json_decode($json_str, true);
    if(!hash_equals($_SESSION['token'], $json_obj['token'])){
        echo JSON_encode("Request forgery detected");
    }
    $event_id = (int)($json_obj['event_id']);
    $event_name = (string)($json_obj['event_name']);
    $event_description = (string)($json_obj['event_description']);
    $tags = (string)($json_obj['tags']);
    $user_id=(string)($json_obj['current_id']);
    if ($tags==""){
        $tags=null;
    }
    $stmt = $mysqli->prepare("SELECT id FROM users where username = ?"); //checks if the id that is passed in matches the id associated with the session username to prevent abuse of functionality
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
        $event_due = (string)($json_obj['event_due']) . " ".(string)($json_obj['event_time']);
        $stmt = $mysqli->prepare("UPDATE events SET Event_Name = ?, Date_Due = ?, Description = ?, Tag =? WHERE Event_ID =?");
        if(!$stmt){
            echo JSON_encode($error_message);
            exit;
        }
        else{
            $stmt->bind_param('ssssi',$event_name,$event_due,$event_description, $tags, $event_id);
            if ($stmt->execute()){
                echo JSON_encode("success");
                exit;
            }
            else{
                echo JSON_encode($stmt->error);
                exit;
            }
        }
    }
?>