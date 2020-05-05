<?php
    ini_set("session.cookie_httponly", 1);
    session_start();
    require 'database.php';
    $json_str = file_get_contents('php://input');
    $json_obj = json_decode($json_str, true);
    if(!hash_equals($_SESSION['token'], $json_obj['token'])){
        echo JSON_encode("Request forgery detected");
        exit;
    }
    $event_name = (string)($json_obj['event_name']);
    $event_description =  (string)($json_obj['event_description']);
    $event_due = (string)($json_obj['event_due']) . " ".(string)($json_obj['event_time']);
    $current_time = date('Y-m-d H:i:s');
    $created_id= (int)($json_obj['created_id']);
    $shared_id=(int)($json_obj['shared_id']);
    $tags = (string)($json_obj['tags']);
    if ($tags==""){
        $tags=null;
    }
    $same = false;
    $stmt = $mysqli->prepare("SELECT id FROM users where username = ?"); //checks if the id that is passed in matches the id associated with the session username to prevent abuse of functionality
    $stmt->bind_param('s',$_SESSION['username']);
    $stmt->execute();
    $stmt->bind_result($check_id);
    while($stmt->fetch()){
        if ($created_id==$check_id){
            $same = true;
        }
        else{
            echo json_encode($check_id);
            //echo json_encode("Abuse of functionality detected!");
            exit;
        }
    }
    $stmt->close();
    if ($same){
        $stmt = $mysqli->prepare("INSERT INTO events (Event_Name, User_ID, Date_Created, Date_Due, Description, Created_ID, Tag) VALUES(?,?,?,?,?,?,?)");
        if(!$stmt){
            echo json_encode($stmt->error);
        }
        else{
            $stmt->bind_param('sisssis',$event_name,$shared_id,$current_time,$event_due,$event_description,$created_id,$tags);
            if ($stmt->execute()){
                echo json_encode("success");
                exit;
            }
            else{
                echo json_encode($stmt->error);
            }
        }
    }
?>