<?php

    session_start();

    if (array_key_exists("content", $_POST)) {
        
        $link = mysqli_connect("127.0.0.1", "root", "", "secretdiary");
    if (mysqli_connect_error()) {
        die("Fix your code!");
    }
        
        $query = "UPDATE `users` SET diary = '".mysqli_real_escape_string($link, $_POST['content'])."' WHERE id = ".mysqli_real_escape_string($link, $_SESSION['id'])." LIMIT 1";
        
        mysqli_query($link, $query);
        
    }

?>
