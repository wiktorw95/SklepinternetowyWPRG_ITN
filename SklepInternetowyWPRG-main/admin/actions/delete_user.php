<?php

include '../../actions/connection.php';

if(isset($_GET['user_id'])){
    $user_id = $_GET['user_id'];
    $stmt = $conn1->prepare("DELETE FROM users WHERE user_id=?");
    $stmt->bind_param("i",$user_id);
    $stmt->execute();

    header('Location: ../users.php?deleted_successfully=User has been deleted' );
    exit;

}