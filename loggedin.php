<?php
session_start();
$diarycontent = "";
if (array_key_exists("id", $_COOKIE) && $_COOKIE['id']) {
    $_SESSION['id'] = $_COOKIE['id'];
}
if (array_key_exists("id", $_SESSION)) {
    $link = mysqli_connect("127.0.0.1", "root", "", "secretdiary");
    if (mysqli_connect_error()) {
        die("Fix your code!");
    }
    $query = "SELECT diary FROM `users` WHERE id ='".mysqli_real_escape_string($link, $_SESSION['id'])."' LIMIT 1";
    $result=mysqli_query($link, $query);
    $row = mysqli_fetch_array($result);
    if(isset($row['diary'])){
        $diarycontent = $row['diary'];
    }
} else {
    header("Location: index.php");
}
?>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>SecretDiary</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
    <style>
        body {
            position: relative;
            margin: 0;
            padding: 0;
            color: white;
            background-image: url('bg.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        textarea {
            margin-top: 30px;
            resize: none;
            height: 100%;
            width: 100%;
        }
    </style>
</head>


<body>
    <div id="topbar">
        <nav class="navbar navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand">Secret Diary</a>
                <a href='index.php?logout=1'><button class="btn btn-outline-success" type="submit">Logout</button></a>
            </div>
        </nav>
    </div>
    <div class="container">
        
            <textarea id="diary" class="form-control"><?php echo $diarycontent; ?></textarea>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.min.js" integrity="sha384-skAcpIdS7UcVUC05LJ9Dxay8AXcDYfBJqt1CJ85S/CFujBsIzCIv+l9liuYLaMQ/" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
    <script type="text/javascript">
        $("#diary").bind('input propertychange', function() {
            $.ajax({
                method: "POST",
                url: "updatedatabase.php",
                data: {
                    content: $("#diary").val()
                },

            });
        });
    </script>
</body>

</html>