<?php
    session_start();

    if(isset($_SESSION['account'])){
        if(!$_SESSION['account']['is_customer']){
            header('location: login.php');
        }
    }else{
        header('location: login.php');
    }
?>
<h2><?= 'Welcome ' . $_SESSION['account']['first_name'] ?></h2>

<a href="product.php">Product</a>

<br>

<a href="logout.php">Logout</a>