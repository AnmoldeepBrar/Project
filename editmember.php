<?php
require('connect.php');
require('authenticate.php');

// Retrieve the member ID from the URL
if (!isset($_GET['id'])) {
    // Handle the case where ID is not provided
    header("Location: members.php"); // Redirect to the members page
    exit;
}

$memberId = $_GET['id'];

// Fetch the member information based on the provided ID
$query = "SELECT * FROM members WHERE id = :member_id";
$statement = $db->prepare($query);
$statement->bindValue(':member_id', $memberId, PDO::PARAM_INT);
$statement->execute();
$member = $statement->fetch(PDO::FETCH_ASSOC);

if (!$member) {
    // Handle the case where member with provided ID doesn't exist
    header("Location: members.php"); // Redirect to the members page
    exit;
}

// Update member information if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve updated information from the form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $contactno = $_POST['contactno'];
    $password = $_POST['password'];

    // Update the member details in the database
    $updateQuery = "UPDATE members SET name = :name, email = :email, role = :role, address = :address, city = :city, contactno = :contactno, password = :password WHERE id = :member_id";
    $updateStatement = $db->prepare($updateQuery);
    $updateStatement->bindValue(':name', $name);
    $updateStatement->bindValue(':email', $email);
    $updateStatement->bindValue(':role', $role);
    $updateStatement->bindValue(':address', $address);
    $updateStatement->bindValue(':city', $city);
    $updateStatement->bindValue(':contactno', $contactno);
    $updateStatement->bindValue(':password', $password);
    $updateStatement->bindValue(':member_id', $memberId, PDO::PARAM_INT);
    $updateStatement->execute();

    // Redirect back to members.php after the update
    header("Location: members.php");
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
    <title>Edit Member</title>
</head>
<body>
    <h1>Edit Member Information</h1>
    <div id="wrapper">
        <form method="post" action="">
            <input type="text" name="name" value="<?= $member['name']; ?>" placeholder="Name" required><br>
            <input type="email" name="email" value="<?= $member['email']; ?>" placeholder="Email" required><br>
            <input type="text" name="role" value="<?= $member['role']; ?>" placeholder="Change role" required><br>
            <input type="text" name="address" value="<?= $member['address']; ?>" placeholder="Address" required><br>
            <input type="text" name="city" value="<?= $member['city']; ?>" placeholder="City" required><br>
            <input type="text" name="contactno" value="<?= $member['contactno']; ?>" placeholder="Contact Number" required><br>
            <input type="password" name="password" value="<?= $member['password']; ?>" placeholder="Password" required><br>
            <button type="submit">Update</button>
        </form>
    </div>
</body>
</html>
