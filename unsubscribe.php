<?php

    require_once 'DBConnection.php';

    if(isset($_GET['email']) && !empty($_GET['email']) && isset($_GET['hash']) && !empty($_GET['hash'])) {
        $email = $_GET['email'];
        $hash = $_GET['hash'];
        $conn = Database::getInstance()->getConnection();
        if(!$conn){
            die('Connection not Established');
        }
        $verify = 1;
        $stmt = $conn->prepare("SELECT * FROM tbl_user WHERE email = ? AND hash = ? AND verify = ?");
        $stmt->bind_param("ssi", $email, $hash, $verify);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0){
            $stmt = $conn->prepare("DELETE FROM tbl_user WHERE email = ? AND hash = ?");
            $stmt->bind_param("ss", $email, $hash);
            $stmt->execute();
            if($stmt->affected_rows > 0){
                echo "
                    <script>alert('Unsubscribe done successfully');
                    window.location.href = 'index.php';
                    </script>
                ";
            } else {
                echo "Unsubscribe Failed";
            }
        } else {
            echo "User Not Found.";
        }

    }