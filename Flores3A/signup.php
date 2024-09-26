<?php
session_start();
//session_start();

//if(isset($_SESSION['account'])){
   // if(!$_SESSION['account']['is_staff']){
     //   header('location: login.php');
   // }
//}else{
    //header('location: login.php');
//}

// Include the functions.php file for utility functions like clean_input, and the product.class.php for database operations.
require_once('functions.php');
require_once('account.class.php');

// Initialize variables to hold form input values and error messages.
$last_name = $first_name = $username = $password = $role = $confirmpassword = "";
$last_nameErr = $first_nameErr = $usernameErr = $passwordErr = $roleErr = $confirmpasswordErr = "";

// Create an instance of the Product class for database interaction.
$AccountObj = new Account();

// Check if the form was submitted using the POST method.
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    // Clean and assign the input values to variables using the clean_input function to prevent XSS or other malicious input.
    $last_name = clean_input($_POST['last_name']);
    $first_name = clean_input($_POST['first_name']);
    $username = clean_input($_POST['username']);
    $password = clean_input($_POST['password']);
    $confirmpassword = clean_input($_POST['confirmpassword']);

    // Validate the 'code' field: check if it's empty or if the code already exists in the database.
    if(empty($last_name)){
        $last_nameErr = 'Last Name is required';
    }

    // Validate the 'name' field: it must not be empty.
    if(empty($first_name)){
        $first_nameErr = 'First Name is required';
    }

    // Validate the 'category' field: it must not be empty.
    if(empty($username)){
        $usernameErr = 'Username is required';
    }elseif($AccountObj->usernameExists($username)){
        $usernameErr = 'Username already exist';
    }

    if(empty($password)){
        $passwordErr = 'password is required';
    }

    if(!($confirmpassword == $password)){
        $confirmpasswordErr = ' Confirm password Does not match';
    }

    if(!$AccountObj->is_strong_password($password, $last_name, $first_name)){
        $passwordErr = 'Your password is weak! You should have a number, a special character and more than 8 letters and password should not be equal to your lastname and firstname';
    }
    

    // If there are no validation errors, proceed to add the product to the database.
    if(empty($last_nameErr) && empty($first_nameErr) && empty($usernameErr) && empty($passwordErr) && empty($confirmpasswordErr)){
        // Assign the sanitized inputs to the product object.
        $AccountObj->last_name = $last_name;
        $AccountObj->first_name = $first_name;
        $AccountObj->password = $password;
        $AccountObj->username = $username;
        if(!empty(($_POST['role'])))
    {
        $role = clean_input($_POST['role']);
        $AccountObj->role = $role;
    } 
        // Attempt to add the product to the database.
        if($AccountObj->add()){
            // If successful, redirect to the product listing page.
            header('Location: product.php');
        } else {
            // If an error occurs during insertion, display an error message.
            echo 'Something went wrong when adding new account.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <style>
        /* Error message styling */
        .error{
            color: red;
        }
    </style>
</head>
<body>
    <!-- Form to collect product details -->
    <form action="" method="post">
        <!-- Display a note indicating required fields -->
        <span class="error">* are required fields</span>
        <br>

        <!-- Product Code field with validation error display -->
        <label for="last_name">Last Name</label><span class="error">*</span>
        <br>
        <input type="text" name="last_name" id="last_name" value="<?= $last_name ?>">
        <br>
        <?php if(!empty($last_nameErr)): ?>
            <span class="error"><?= $last_nameErr ?></span><br>
        <?php endif; ?>

        <!-- Product Name field with validation error display -->
        <label for="name">First Name</label><span class="error">*</span>
        <br>
        <input type="text" name="first_name" id="first_name" value="<?= $first_name ?>">
        <br>
        <?php if(!empty($first_nameErr)): ?>
            <span class="error"><?= $first_nameErr ?></span><br>
        <?php endif; ?>


        <label for="user">Username</label><span class="error">*</span>
        <br>
        <input type="text" name="username" id="username" value="<?= $username ?>">
        <br>
        <?php if(!empty($usernameErr)): ?>
            <span class="error"><?= $usernameErr ?></span><br>
        <?php endif; ?>

        <label for="password">Password</label><span class="error">*</span>
        <br>
        <input type="password" name="password" id="password" value="<?= $password ?>">
        <br>
        <?php if(!empty($passwordErr)): ?>
            <span class="error"><?= $passwordErr ?></span><br>
        <?php endif; ?>
        <label for="confirmpassword">Confirm Password</label><span class="error">*</span>
        <br>
        <input type="password" name="confirmpassword" id="confirmpassword" value="<?= $password ?>">
        <br>
        <?php if(!empty($confirmpasswordErr)): ?>
            <span class="error"><?= $confirmpasswordErr ?></span><br>
        <?php endif; ?>
       
        <label for="role">role</label><span class="error">*</span>
        <br>
        <input type="radio" name="role" id="role" value="staff" <?php if($role == 'staff'){echo "checked";}?>> Staff
        <input type="radio" name="role" id="role" value="admin" <?php if($role == 'admin'){echo "checked";}?>> Admin
        <br>
        <br>
        <input type="submit" value="Save Account">
    </form>
</body>
</html>
