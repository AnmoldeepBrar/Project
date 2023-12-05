<?php

require('connect.php');
require('authenticate.php');

$editmember = "editmember.php";
$about = "about.php";

$query = "SELECT * FROM members";
$statement = $db->prepare($query);
$statement->execute();
$members = $statement->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_member'])) {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $contactno = filter_input(INPUT_POST, 'contactno', FILTER_VALIDATE_INT);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
/*    
    if (empty($confirmpassword)) {
        $errors[] = "Confirm Password is required.";
    } elseif ($confirmpassword !== $password) {
        $errors[] = "Password and Confirm Password do not match.Please try again.";
    } */

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
    if (empty($role)) {
        $errors[] = "Please assign role to the user.";
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

            $query = "INSERT INTO `members` (name, email, role, address, city, contactno, password) VALUES (:name, :email, :role, :address, :city, :contactno, :password)";
            $statement = $db->prepare($query);
            $statement->bindValue(':name', $name);
            $statement->bindValue(':email', $email);
            $statement->bindValue(':role', $role);
            $statement->bindValue(':address', $address);
            $statement->bindValue(':city', $city);
            $statement->bindValue(':contactno', $contactno);
            $statement->bindValue(':password', $hashedPassword); // Store the hashed password
           // $statement->bindValue(':confirmpassword', $confirmpassword);
            $statement->execute();
            header("Location: members.php");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action']) && isset($_POST['members_id'])) {
        $action = $_POST['action'];
        $memberId = $_POST['members_id'];

        switch ($action) {
            case 'delete':
                $deleteQuery = "DELETE FROM members WHERE id = :members_id";
                $deleteStatement = $db->prepare($deleteQuery);
                $deleteStatement->bindValue(':members_id', $memberId, PDO::PARAM_INT);
                $deleteStatement->execute();
                break;
        }

        // Refresh comments after performing action
        $statement->execute();
        $members = $statement->fetchAll(PDO::FETCH_ASSOC);
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
</head>
<body>
    <h1>@Brar Book Store</h1>
    <div id="wrapper">
    <form method="get" action="<?= $about; ?>">
        <button type="submit">Back</button>
    </form>
    <h2>Create New Member</h2>
    <form method="post" action="">
            <label for="name">Full Name</label>
            <input id="name" name="name"><br>
            <label for="email">Email Address</label>
            <input id="email" name="email"><br>
            <label for="role">Role</label>
            <input type="text" name="role" placeholder="member or admin"><br>
            <label for="address">Address</label>
            <input id="address" name="address"><br>
            <label for="city">City</label>
            <input id="city" name="city"><br>
            <label for="contactno">Contact Number</label>
            <input id="contactno" name="contactno"><br>
            <label for="password">Set Password</label>
            <input id="password" name="password"><br>
            <button type="submit" name="create_member">Create Member</button>
    </form>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email Address</th>
                    <th>Role</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>Contact Number</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($members as $member) : ?>
                    <tr>
                        <td><?= $member['id']; ?></td>
                        <td><?= $member['name']; ?></td>
                        <td><?= $member['email']; ?></td>
                        <td><?= $member['role']; ?></td>
                        <td><?= $member['address']; ?></td>
                        <td><?= $member['city']; ?></td>
                        <td><?= $member['contactno']; ?></td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="members_id" value="<?= $member['id']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit">Delete</button>
                                <!-- Add other buttons for hide and disemvowel actions -->
                                <!-- <button type="submit" name="action" value="hide">Hide</button>
                                <button type="submit" name="action" value="disemvowel">Disemvowel</button> -->
                            </form>
                            <form method="post" action="">
                            <button type="button" onclick="editMember(<?= $member['id']; ?>)">Edit</button>
                            </form> 
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script>
    function editMember(memberId) {
        // Redirect to a separate PHP page for editing based on member ID
        window.location.href = `editmember.php?id=${memberId}`;
    }
</script>
</body>
</html>
