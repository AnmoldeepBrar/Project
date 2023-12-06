<?php
/*******w******** 
    
    Name: Anmoldeep Kaur
    Date: 25 September 2023
    Description: This is my first website.

****************/

session_start();
require('connect.php');

$loginPage = "login.php";
$signupPage = "signup.php";
$home = "home.php";
$category = "category.php";
$collection = "collection.php";
$login = "signup.php";
$about = "about.php";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (empty($email) || empty($password)) {
        $errors[] = "Email and password are required. Please fill in all the information.";
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $password = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    
        if (empty($email) || empty($password)) {
            $errors[] = "Email and password are required. Please fill in all the information.";
        }
    
        if (empty($errors)) {
            try {
                // Check if the user exists in the members table
                $query = "SELECT * FROM `members` WHERE email = :email";
                $statement = $db->prepare($query);
                $statement->bindValue(':email', $email);
                $statement->execute();
    
                $user = $statement->fetch(PDO::FETCH_ASSOC);
    
                if ($user) {
                    echo "Entered Email: $email<br>";
                    echo "Entered Password: $password<br>";
                    echo "Retrieved Hashed Password from DB: {$user['password']}<br>";
    
                    if (password_verify($password, $user['password'])) {
                        // Password is correct, set session variables and redirect
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['loggedin'] = true;
                        header("Location: home.php");
                        exit;
                    } else {
                        // Incorrect password
                        $errors[] = "Incorrect email or password. Please enter the correct credentials.";
                    }
                } else {
                    // User not found
                    $errors[] = "User not found. Please register an account.";
                }
            } catch (PDOException $e) {
                $errors[] = "Database error: " . $e->getMessage();
            }
        }
    }
}

// Logout functionality
if (isset($_POST['logout'])) {
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    header("Location: $loginPage");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>@Brar Book Store</title>
</head>
<body>
<div id="wrapper">
    <h1>@Brar Book Store: Online Library</h1>
    <ul id="links">
        <li><a href="<?= $home; ?>">Home Page</a></li>
        <li><a href="<?= $category; ?>">Categories</a></li>
        <li><a href="<?= $collection; ?>">Books</a></li>
        <li><a href="<?= $login; ?>">Sign Up</a></li>
    </ul>
    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <div id="error"><?= $error; ?></div>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php elseif (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
        <div id="wrapper">
            <p>Welcome! You are logged in.</p>
            <form method="post" action="">
                <button type="submit" name="logout">Logout</button>
            </form>
        </div>
    <?php else: ?>
            <p>Login to your account</p>
            <form method="post" action="<?= $loginPage; ?>">
                <!-- Login form fields -->
                <label for="email">Email Address</label>
                <input id="email" name="email"><br>
                <label for="password">Password</label>
                <input id="password" name="password" type="password"><br>
                <button>Login</button>
            </form>
            <p>Don't have an account? <a href="<?= $signupPage; ?>">Sign Up</a></p>
        </div>
    <?php endif; ?>
</body>
</html>
