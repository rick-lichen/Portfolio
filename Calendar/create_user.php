<?php 
    require 'database.php';
    header("Content-Type: application/json");
    //Accepting the json file with the inputs from the html (using the wiki page)
    $json_str = file_get_contents('php://input');
    $json_obj = json_decode($json_str, true);
    $user = (string)($json_obj['username']);
    if( !preg_match('/^[\w_\-]+$/', $user) ){       //Filtering input
        echo json_encode(array(
            "success"=> false,
            "message"=>"Invalid username. Please remove special characters."));
        exit;
    }
    $pass = (string)$json_obj['password'];
    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare("insert into users (username, pass) values (?,?)");
    if (!$stmt){
        echo json_encode(array(
            "success"=> false,
            "message"=>"Query Prep Failed:"+$mysqli->error));
    	exit;
    }
    $stmt->bind_param('ss', $user, $hashed_pass);
    if ($stmt->execute()) { // query successful
        echo json_encode(array(
            "success" => true,
            "message" => htmlentities($user).' has been created!'
        ));
    } else{
        echo json_encode(array(
            "success" => false,
            "message" => $stmt->error
        ));      //insert unsuccessful, print out message
    }
    
?>