<?php
/*******w********
    Name: Anmoldeep Kaur
    Date: 25 September 2023
    Description: This is my first website.
****************/

require('connect.php');
require('authenticate.php');

$post = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete']) && isset($_POST['id'])) {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

        $query = "DELETE FROM `books` WHERE id = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        header("Location: view.php");
        exit;
    }

    if (isset($_POST['title']) && isset($_POST['author']) && isset($_POST['id']) && isset($_POST['description']) && isset($_POST['avaliablecopies']) && isset($_POST['totalcopies'])) {
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $avaliablecopies = filter_input(INPUT_POST, 'avaliablecopies', FILTER_SANITIZE_NUMBER_INT);
        $totalcopies = filter_input(INPUT_POST, 'totalcopies', FILTER_SANITIZE_NUMBER_INT);
        $posttime = date('Y-m-d h:i:s');

        $query = "UPDATE `books` SET title = :title, author = :author, description = :description, avaliablecopies = :avaliablecopies, totalcopies = :totalcopies, posttime = :posttime WHERE id = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':title', $title);
        $statement->bindValue(':author', $author);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':avaliablecopies', $avaliablecopies);
        $statement->bindValue(':totalcopies', $totalcopies);
        $statement->bindValue(':posttime', $posttime);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        header("Location: view.php");
        exit;
    }
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    $query = "SELECT * FROM `books` WHERE id = :id";
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
<div id = "wrapper">
    <h1>@Brar Book Store: Online Library</h1>
    <ul id="links">
        <li><a href="collection.php">Book List</a></li>
        <li><a href="view.php">See your full Catalogue</a></li>
        <li><a href="create.php">Add book to your Collection</a></li>
    </ul>
    <?php if ($post): ?>
    <p>Edit the book:</p>
    <form method="post" action="edit.php">
        <input type="hidden" name="id" value="<?= $post['id']; ?>">
        <label for="title">Title</label>
        <input id="title" name="title" value="<?= $post['title']; ?>"><br><br>
        <label for="author">Author</label>
        <input id="author" name="author" value="<?= $post['author']; ?>"><br><br>
        <label for="image">Picture</label>
        <?php if ($post['image']): ?>
            <img src="<?= $post['image']; ?>" alt="<?= $post['title']; ?>" style="max-width: 150px; max-height: 150px;">
        <?php else: ?>
            No Image Available
        <?php endif; ?>
        <a href="editimage.php?book_id=<?= $post['id']; ?>">Update the image</a>
            <?php if (!empty($_GET['success']) && $_GET['success'] == 1): ?>
                <div id="success_message">
                    Image has been updated successfully.
                </div>
            <?php endif; ?>
        <label for="description">Description</label>
        <textarea id="description" name="description"><?= $post['description']; ?></textarea><br><br>
        <label for="avaliablecopies">Available Copies</label>
        <input id="avaliablecopies" name="avaliablecopies" value="<?= $post['avaliablecopies']; ?>"><br><br>
        <label for="totalcopies">Total Copies</label>
        <input id="totalcopies" name="totalcopies" value="<?= $post['totalcopies']; ?>"><br><br>
        <button type="submit">Update</button>
    </form>
    <form method="post" action="edit.php">
            <input type="hidden" name="id" value="<?= $post['id']; ?>">
            <input type="hidden" name="delete">
            <button type="submit">Delete</button>
    </form>
<?php else: ?>
    <p>Book is not available.</p>
<?php endif; ?>
</div>
</body>
</html>
