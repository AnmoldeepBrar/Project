<?php
/**********
    Name: Anmoldeep Kaur
    Date: 25 September 2023
    Description: This is my first website.
**********/

require('connect.php');
require('authenticate.php');

$post = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete']) && isset($_POST['id'])) {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

        $query = "DELETE FROM `category` WHERE id = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        header("Location: viewcategory.php");
        exit;
    }

    if (isset($_POST['type']) &&  isset($_POST['id']) && isset($_POST['description'])) {
        $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $query = "UPDATE `category` SET type = :type, description = :description WHERE id = :id"; // Removed the comma after :description
        $statement = $db->prepare($query);
        $statement->bindValue(':type', $type);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        header("Location: category.php");
        exit;
    }
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    $query = "SELECT * FROM `category` WHERE id = :id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->execute();
    $post = $statement->fetch(PDO::FETCH_ASSOC);
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
    <h1>@Brar Book Store: Online Library</h1>
    <ul id="links">
        <li><a href="category.php">See your full Catalogue</a></li>
        <li><a href="viewcategory.php">Add book to your Collection</a></li>
    </ul>
    <?php if ($post): ?>
    <p>Edit the category:</p>
    <form method="post" action="editcategory.php">
        <input type="hidden" name="id" value="<?= $post['id']; ?>">
        <label for="type">Category</label>
        <input id="type" name="type" value="<?= $post['type']; ?>"><br><br>
        <label for="description">Description</label>
        <textarea id="description" name="description"><?= $post['description']; ?></textarea><br><br>
        <button type="submit">Update</button>
    </form>
    <form method="post" action="category.php">
            <input type="hidden" name="id" value="<?= $post['id']; ?>">
            <input type="hidden" name="delete">
            <button type="submit">Delete</button>
    </form>
<?php else: ?>
    <p>Category is not found.</p>
<?php endif; ?>
</body>
</html>
