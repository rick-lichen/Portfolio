<?php 
    require 'database.php';
    header("Content-Type: application/json");
    //Accepting the json file with the inputs from the html (using the wiki page)
    $json_str = file_get_contents('php://input');
    $json_obj = json_decode($json_str, true);
    $user = (string)($json_obj['username']);
    if(!preg_match('/^[\w_\-]+$/', $user) ){       //Filtering input
        echo json_encode(array(
            "success"=> false,
            "message"=>"Invalid username. Please remove special characters."));
        exit;
    }
    $pass = (string)($json_obj['password']);
    $stmt = $mysqli->prepare("SELECT COUNT(*), id, pass FROM users WHERE username=?");
    //Binding input
    $stmt->bind_param('s', $user);
    $stmt->execute();
    //Binding results
    $stmt->bind_result($cnt, $user_id, $pwd_hash);
    $stmt->fetch();
    if($cnt == 1 && password_verify($pass, $pwd_hash)){     //Compares password entered to actual password
        session_start();
        $_SESSION['username'] = $user;
        $_SESSION['user_id'] = $user_id;
        $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32)); 

        echo json_encode(array(
            "success" => true,
            "username" => htmlentities($user)
        ));
        exit;
    }
    else{
        echo json_encode(array(
            "success" => false,
            "message" => htmlentities("Incorrect Username or Password")
        ));
        exit;
    }
// 
// <?php session_start();
//     require 'database.php';
//     if (isset($_SESSION['username'])){
//         echo "You have already logged in, redirecting...";
//         header("refresh:3;url=frontpage.php");
//     } else{
//         if (isset($_POST['username'])){
//             $user = $_POST['username'];
//             if( !preg_match('/^[\w_\-]+$/', $user) ){       //Filtering input
//                 echo "Invalid username. Please remove special characters. Redirecting...";
//                 header("refresh:4;url=login.html");
//                 exit;
//             }
//             $pass = (String)($_POST['password']);
//             $stmt = $mysqli->prepare("SELECT COUNT(*), id, pass FROM users WHERE username=?");
//             //Binding input
//             $stmt->bind_param('s', $user);
//             $stmt->execute();
//             //Binding results
//             $stmt->bind_result($cnt, $user_id, $pwd_hash);
//             $stmt->fetch();
//             if($cnt == 1 && password_verify($pass, $pwd_hash)){     //Compares password entered to actual password
//                 // Login succeeded!
//                 $_SESSION['username']=$user;
//                 $_SESSION['user_id'] = $user_id;
//                 $_SESSION['loggedin']=true;
//                 $_SESSION['token']=bin2hex(random_bytes(32));
//                 echo "Login successful. Redirecting...";
//                 header("refresh:3;url=calendar.php");
//                 // Redirect to your target page
//             } else{
//                 // Login failed; redirect back to the login screen
//                 echo "Login failed. Redirecting...";
//                 header("refresh:3;url=login.html");
//             }
//         } else{
//             echo "Please login through the login page. Redirecting...";
//             header("refresh:3;url=login.html");
//         }
//     }

        
?>