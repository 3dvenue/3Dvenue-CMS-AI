<?php
/* 3Dvenue-CMS Copyright (c) 2026 yoshihiro Licensed under MIT (https://opensource.org/licenses/MIT)*/
session_start();

$account = "Your-Account";
$password = "Your-Password";

$title="3Dvenue-CMS";
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['account']) && isset($_POST['password'])) {
        if($_POST['account'] === $account && $_POST['password'] === $password){
            $_SESSION['ADMIN_CHECK'] = "success";
            header("Location: index.php");
            exit;
        }else{
            $_SESSION = array();
            session_destroy();
            $message = "Invalid email address or password.";
        }
    }

}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="./css/style.css">
    <link rel="icon" href="../favicon.ico">
    <title>
        <?=$title?>
    </title>
<style type="text/css">
.inner{
    max-width:680px;
}

h2{
    text-align: center;
}

.error{
    text-align: center;
    margin-bottom:40px;
}

#form{
    max-width: 420px;
    border:1px solid #999;
    background:linear-gradient(#FFF,#EEE);
    padding:20px 40px;
    border-radius: 10px;
    margin: 0 auto;
}

main section{
    height:100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

main section h2{
    margin-bottom:20px;
}

form label{
    display: flex;
    justify-content: space-between;
    padding:0 0 20px;
}

form label input{
    padding:5px 10px;
    border:1px solid #999;
    border-radius: 5px;
}

#submitButton{
    text-align: right;
}

#message{
    text-align: center;
    font-weight:700;
    font-size:12px;
    color:red;
}

</style>
</head>
<body>
<main>
<section id="login">
    <div class="inner">
    <h2><?=$title?></h2>
    <div id="form">
        <form method="POST">
            <div id="message"><?=$message?></div>
            <label><span>Account：</span><input type="text" name="account" value="" placeholder="acount@example.com" required></label>
            <label><span>password：</span><input type="text" name="password" value="" placeholder="password" required></label>
            <div id="submitButton"><button type="submit" class="btn" name="submit" value="login">ログイン</button>
        </form>
    </div>
</section>
</div>
</main>
</body>
</html>