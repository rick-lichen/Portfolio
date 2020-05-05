<?php
    ini_set("session.cookie_httponly", 1);
    session_start();
    require 'database.php';
    $same = false;
    $json_str = file_get_contents('php://input');
    $json_obj = json_decode($json_str, true);
    if(!hash_equals($_SESSION['token'], $json_obj['token'])){
        die("Request forgery detected");
    }
    $event_id = $json_obj['event_id'];
    $user_id = $json_obj['current_id'];
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
        $stmt = $mysqli->prepare("DELETE FROM events WHERE event_id = ?");
        $stmt->bind_param('i',$event_id);
        if(!$stmt){
            echo JSON_encode("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }    
        if ($stmt->execute()) { // deleting event is successful
            echo JSON_encode('Event has been deleted!');
            exit;
        } else{
            echo JSON_encode($stmt->error);        //deleting event , print out message
            exit;
        }
    }
?>