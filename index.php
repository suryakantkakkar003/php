<?php

    require_once 'DBConnection.php';

    $alert = '';

    if(isset($_POST['email']) && !empty($_POST['email'])){
        $email = htmlspecialchars($_POST['email']);
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        $hash = md5($randomString);
        $conn = Database::getInstance()->getConnection();
        if(!$conn){
            die('Connection not Established');
        }
        $stmt = $conn->prepare("SELECT * FROM tbl_user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO tbl_user (email, hash) VALUES (?, ?)");
            $stmt->bind_param("ss", $email, $hash);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $to = $email;
                $subject = "Verification";
                $url = "https://github.com/suryakantkakkar003/php/blob/main/verify.php?email=$email&hash=$hash";
                $msg = "
                    <html>
                        <head>
                            <title>Verification</title>
                        </head>
                        <body>
                            Verification Link<br />
                            <a target='_blank' href=$url>Click to Verify</a>
                        </body>
                    </html>
                ";
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: suryakantkakkar@gmail.com" . "\r\n";

                mail($to, $subject, $msg, $headers);

                $alert = 'Verification Mail sent to your email address.';

            } else {
                echo "Error Occured";
            }
        } else {
            $alert = "Email already registered";
        }
    }
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>XKCD Challenge</title>
        <style>
            input{
              width: 100%;
              padding: 12px 20px;
              margin: 8px 0;
              display: inline-block;
              border: 2px solid #ccc;
              border-radius: 6px;
              box-sizing: border-box;
            }
            
            div {
              border-radius: 5px;
              background-color: #f2f2f2;
              padding: 20px;
            }
            
            button {
              width: 100%;
              background-color: #4CAF50;
              color: white;
              padding: 14px 20px;
              margin: 8px 0;
              border: none;
              border-radius: 4px;
              cursor: pointer;
            }
            
            button:hover {
              background-color: #45a049;
            }
        </style>
    </head>
    <body>
    <?php
        if(!empty($alert)){
            echo "
                <script>alert('$alert');</script>
            ";
        }

    ?>
        <div>
            <h1>Subscribe for XKCD Images</h1>
            <form method="POST" action="">
                <div class="form-group">
                    <h3>Email address</h3>
                    <input type="email" name="email" placeholder="Enter email" required>
                    <small>We"ll never share your email with anyone else.</small>
                </div>
                <button type="submit">Submit</button>
            </form>
        <div>
        
    </body>
</html>
