<?php
/*******w******** 
    
    Name: Anmoldeep Kaur
    Date: 25 September 2023
    Description: This is my first website.

****************/

require('connect.php');

$login = "signup.php";
$loginlink = "login.php";
$home = "home.php";
$category = "category.php";
$collection = "collection.php";
$login = "signup.php";
$about = "about.php";
//$insert = "create.php";  
//$view = "view.php";    


$errors = [];
//$posttime = date('Y-m-d H:i:s', time());

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $contactno = filter_input(INPUT_POST, 'contactno', FILTER_VALIDATE_INT);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $confirmpassword = filter_input(INPUT_POST, 'confirmpassword', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   
    if (empty($confirmpassword)) {
        $errors[] = "Confirm Password is required.";
    } elseif ($confirmpassword !== $password) {
        $errors[] = "Password and Confirm Password do not match.Please try again.";
    }

    // Password strength criteria
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number = preg_match('@[0-9]@', $password);
    $specialChar = preg_match('@[\W]@', $password);

    if (empty($name)) {
        $errors[] = "Full Name is required. Please enter your name.";
    }
    if (empty($email)) {
        $errors[] = "Please enter your valid email address.";
    }
    if (empty($address)) {
        $errors[] = "Address is required. Please enter your address.";
    }
    if (empty($city)) {
        $errors[] = "City is required. Please enter your city.";
    }
    if (empty($contactno)) {
        $errors[] = "Please enter your valid contact number.";
    }
    if (empty($password)) {
        $errors[] = "Create your password.";
    } elseif (!$uppercase || !$lowercase || !$number || !$specialChar || strlen($password) < 8) {
        $errors[] = "Password should be at least 8 characters long and include uppercase, lowercase, number, and special characters.";
    }

    if (empty($errors)) {
        try {
            // Hash the password
            $password = $_POST['password'];
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
           // $hashedConfirmPassword = password_hash($confirmpassword, PASSWORD_DEFAULT);
    

            $checkQuery = "SELECT COUNT(*) as count FROM `members` WHERE email = :email";
            $checkStatement = $db->prepare($checkQuery);
            $checkStatement->bindValue(':email', $email);
            $checkStatement->execute();
            $result = $checkStatement->fetch(PDO::FETCH_ASSOC);
        
            if ($result['count'] > 0) {
                $errors[] = "User already exists with the provided email.";
            }

            $query = "INSERT INTO `members` (name, email, address, city, contactno, password) VALUES (:name, :email, :address, :city, :contactno, :password)";
            $statement = $db->prepare($query);
            $statement->bindValue(':name', $name);
            $statement->bindValue(':email', $email);
            $statement->bindValue(':address', $address);
            $statement->bindValue(':city', $city);
            $statement->bindValue(':contactno', $contactno);
            $statement->bindValue(':password', $hashedPassword); // Store the hashed password
           // $statement->bindValue(':confirmpassword', $confirmpassword);
            $statement->execute();
            header("Location: collection.php");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
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
    <script>
        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const toggleBtn = document.querySelector(`#${inputId} + .toggle-password`);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.textContent = '👁️';
            } else {
                passwordInput.type = 'password';
                toggleBtn.textContent = '👁️';
            }
        }
    </script>
</head>
<body>
    <h1>@Brar Book Store: Online Library</h1>
    <ul id="links">
        <li><a href="<?= $home; ?>">Home Page</a></li>
        <li><a href="<?= $category; ?>">Categories</a></li>
        <li><a href="<?= $login; ?>">Sign Up</a></li>
        <li><a href="<?= $about; ?>">Settings</a></li>
    </ul>
    <?php if (!empty($errors)): ?>
        <div class="error">
        <ul>
            <?php $uniqueErrors = array_unique($errors); ?>
            <div id="error"><?= reset($uniqueErrors); ?></div>
        </ul>
        </div>
    <?php else: ?>
        <div id="wrapper">
        <p>Create your account</p>
        <form method="post" action="<?= $login; ?>">
            <label for="name">Full Name</label>
            <input id="name" name="name"><br>
            <label for="email">Email Address</label>
            <input id="email" name="email"><br>
            <label for="address">Address</label>
            <input id="address" name="address"><br>
            <label for="city">City</label>
            <input id="city" name="city"><br>
            <label for="contactno">Contact Number</label>
            <input id="contactno" name="contactno"><br>
            <label for="password">Create Password</label>
                <div class="password-toggle">
                    <input id="password" name="password" type="password">
                    <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
                </div><br>
            <label for="confirmpassword">Confirm Password</label>
                <div class="password-toggle">
                    <input id="confirmpassword" name="confirmpassword" type="password">
                    <span class="toggle-password" onclick="togglePassword('confirmpassword')">👁️</span>
                </div><br>
            <button>Sign Up</button>
        </form>
        <p>Already have an account? <a href="<?= $loginlink; ?>">Login</a></p>
        </div>
    <?php endif; ?>
</body>
</html>