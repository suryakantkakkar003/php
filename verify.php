<?php

    require_once 'DBConnection.php';

    if(isset($_GET['email']) && !empty($_GET['email']) && isset($_GET['hash']) && !empty($_GET['hash'])){
        $conn = Database::getInstance()->getConnection();
        $email = htmlspecialchars($_GET['email']);
        $hash = htmlspecialchars($_GET['hash']);
        if(!$conn){
            die('Connection not Established');
        }
        $stmt = $conn->prepare("SELECT * FROM tbl_user WHERE email = ? AND hash = ?");
        $stmt->bind_param("ss", $email, $hash);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0){
            $stmt = $conn->prepare("UPDATE tbl_user SET verify = 1 WHERE email = ? AND hash = ?");
            $stmt->bind_param("ss", $email, $hash);
            $stmt->execute();
            if($stmt->affected_rows > 0){
                echo "
                    <script>alert('Verification done successfully');
                    window.location.href = 'index.php';
                    </script>
                ";
            } else {
                if($stmt->errno == 0){
                    echo "Already Verified";
                } else {
                    echo "Verificaiton Failed";
                }
            }
        } else {
            echo "Email Not Found.";
        }
    }