<?php
    ini_set("session.cookie_httponly", 1);
    session_start();
    require 'database.php';
    header("Content-Type: application/json");
    $json_str = file_get_contents('php://input');
    $json_obj = json_decode($json_str, true);
    $same = false;
    if(!hash_equals($_SESSION['token'], $json_obj['token'])){
        echo JSON_encode("Request forgery detected");
    }
    $date = $json_obj['date'].'%';  //the % is for sql wildcard character to ignore time in date
    if (($json_obj['userid'])==null){
        $user_id = 'hi';
    } else{
        $user_id = ($json_obj['userid']);
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
        $stmt = $mysqli->prepare("SELECT Event_Name, Description, Event_ID, Date_Due, Tag FROM events WHERE Date_Due LIKE ? && User_ID = ? ORDER BY Date_Due");
        if (!$stmt){
            echo JSON_encode(array("event_error"=>($stmt->error)));
            exit;
        }
        $stmt->bind_param('ss', $date, $user_id);
        $stmt->execute();
        $stmt->bind_result($event_name, $event_description, $event_id, $time, $tag);
        $event_name_array = array();
        $event_description_array = array();
        $date_due_array = array();
        $event_id_array = array();
        $time_array = array();
        $event_tag_array= array();
        while($stmt->fetch()){
            array_push($event_name_array,htmlentities($event_name));
            array_push($event_description_array,htmlentities($event_description));
            array_push($date_due_array,htmlentities($json_obj['date']));
            array_push($event_id_array,htmlentities($event_id));
            array_push($event_tag_array,htmlentities($tag));
            $due_exploded = explode(" ", $time);
            $time_of_event = $due_exploded[1];
            array_push($time_array, htmlentities($time_of_event));
        }
        echo JSON_encode(array("event_names"=>$event_name_array,"event_descriptions"=>$event_description_array, "date"=>$date_due_array, "event_id"=>$event_id_array, "time"=>$time_array, "event_tags"=>$event_tag_array));
        exit;
    }
?>