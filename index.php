<?php
session_start();
$error = "";
$success = "";
if (array_key_exists("logout", $_GET)) {
    unset($_SESSION['id']);
    setcookie("id", "", time() - 60 * 60);
    $_COOKIE["id"] = "";
    session_destroy();
} else if ((array_key_exists("id", $_SESSION) and $_SESSION['id']) or (array_key_exists("id", $_COOKIE) and $_COOKIE['id'])) {
    header("Location: loggedin.php");
}
if (array_key_exists("submit", $_POST)) {
    $link = mysqli_connect("127.0.0.1", "root", "", "secretdiary");
    if (mysqli_connect_error()) {
        die("Fix your code!");
    }
    if (!$_POST["email"]) {
        $error .= "<br> Email is required! <br>";
    }
    if (!$_POST["password"]) {
        $error .= "Password is required! <br>";
    }
    if ($error != "") {
        $error = '<div class="alert alert-danger" role="alert">' . "<strong>There were error(s) in your submission:</strong>" . $error . '</div><br><br>';
    } else {
        if ($_POST['signup'] == '1') {
            $query = "SELECT id FROM `users` WHERE email ='" . mysqli_real_escape_string($link, $_POST['email']) . "'LIMIT 1";
            $result = mysqli_query($link, $query);
            if (mysqli_num_rows($result) > 0) {
                $error .= "Email already exists";
            } else {
                $query = "INSERT INTO `users`(`email`,`password`) VALUES('" . mysqli_real_escape_string($link, $_POST['email']) . "','" . mysqli_real_escape_string($link, $_POST['password']) . "')";
                if (!mysqli_query($link, $query)) {
                    $error = "Could not sign you up! Please try again later.";
                } else {
                    $_SESSION['id'] = $id;
                    $query = "UPDATE `users` SET password ='".md5(md5(mysqli_insert_id($link)) . $_POST['password'])."' WHERE id='".mysqli_insert_id($link) ."'";
                    $id = mysqli_insert_id($link);
                    mysqli_query($link, $query);
                    if ($_POST['stay'] == '1') {
                        setcookie("id", $id, time() + 60 * 60 * 24 * 365);
                    }
                    header("Location: loggedin.php");
                }
            }
        } else {
            $query = "SELECT * FROM `users` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."'";
            $result = mysqli_query($link, $query);
            $row = mysqli_fetch_array($result);
            if (isset($row)) {
                $hashed = md5(md5($row['id']) . $_POST['password']);
                if ($hashed == $row['password']) {
                    $_SESSION['id'] = $row['id'];
                    if (isset($_POST['stay']) AND $_POST['stay'] == '1') {
                        setcookie("id", $row['id'], time() + 60 * 60 * 24 * 365);
                    }
                    header("Location: loggedin.php");
                } else {
                    $error .= "Incorrect email/password!";
                }
            } else {
                $error .= "Incorrect email/password!";
            }
        }
        if ($error != "") {
            $error = '<div class="alert alert-danger" role="alert">' . "<strong>There were error(s) in your submission:</strong>" . $error . '</div><br><br>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

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

        .container {
            width: 50%;
            margin: auto;
            position: relative;
            top: 170px;
        }

        .content {
            text-align: center;
            align-items: center;
            align-content: center;
        }

        input[type="checkbox"]{
            display: inline-block;
            vertical-align: middle;
            position: relative;
            display: flex; 
            align-items: center;
        }
        #staytext {
            position: relative;
            float: left;
        }
        .stay{
            align-items: center;
        }
        #signupform {
            margin-top: 15px;
        }

        #loginform {
            display: none;
        }

        #signupform {
            display: block;
        }

        .toggle {
            color: beige;
            font-weight: bold;
        }

        #error {
            width: 70%;
            margin: auto;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="content">
            <h1>Secret Diary</h1>
            <h5>Store your thoughts permanently and securely.</h5>
            <div id="error"><?php echo ($error . $success); ?></div>
            <div>
                <form method="POST" id="signupform">
                    <p>Interested? Sign up now.</p>
                    <div class="mb-3">
                        <input type="email" class="form-control" id="email" placeholder="Email Address" name="email">
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" id="password" placeholder="Password" name="password">
                    </div>
                    <div class="mb-3 form-check stay">
                        <input type="checkbox" class="form-check-input" id="stay" name="stay" value=1>
                        <label class="form-check-label" for="stay" name="stay" id="staytext">Stay logged in</label>
                    </div>
                    <input type="hidden" name="signup" value="1">
                    <button type="submit" class="btn btn-success" id="submit" name="submit">Sign Up!</button>
                    <p></p>
                    <p><a class="toggle">Already have an account? Log In!</a></p>
                </form>
            </div>
            <div>
                <form method="POST" id="loginform">
                    <p>Log in securely with your credentials!</p>
                    <div class="mb-3">
                        <input type="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="Email Address" name="email">
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" id="password" placeholder="Password" name="password">
                    </div>
                    <div class="mb-3 form-check stay">
                        <input type="checkbox" class="form-check-input" id="stay" name="stay" value=1>
                        <label class="form-check-label" for="stay" name="stay" id="staytext">Stay logged in</label>
                    </div>
                    <input type="hidden" name="signup" value="0">
                    <button type="submit" class="btn btn-success" id="submit" name="submit">Log In!</button>
                    <p></p>
                    <p><a class="toggle">Don't have an account? Sign Up</a></p>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.min.js" integrity="sha384-skAcpIdS7UcVUC05LJ9Dxay8AXcDYfBJqt1CJ85S/CFujBsIzCIv+l9liuYLaMQ/" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
    <script type="text/javascript">
        $(".toggle").click(function() {
            $("#signupform").toggle();
            $("#loginform").toggle();
        });
        $(".toggle").hover(function() {
            $(".toggle").css("color", "cyan");
        }, function() {
            $(".toggle").css("color", "beige");
        });
    </script>
</body>

</html>