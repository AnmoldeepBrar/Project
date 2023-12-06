<?php

require('connect.php');
require('authenticate.php');

$about = "about.php";

$query = "SELECT * FROM comments";
$statement = $db->prepare($query);
$statement->execute();
$comments = $statement->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action']) && isset($_POST['comment_id'])) {
        $action = $_POST['action'];
        $commentId = $_POST['comment_id'];

        switch ($action) {
            case 'delete':
                $deleteQuery = "DELETE FROM comments WHERE id = :comment_id";
                $deleteStatement = $db->prepare($deleteQuery);
                $deleteStatement->bindValue(':comment_id', $commentId, PDO::PARAM_INT);
                $deleteStatement->execute();
                break;
            case 'hide':
                // Implement hiding comment logic
                break;
            case 'disemvowel':
                // Implement disemvoweling comment logic
                break;
            default:
                // Handle other actions or errors
                break;
        }

        // Refresh comments after performing action
        $statement->execute();
        $comments = $statement->fetchAll(PDO::FETCH_ASSOC);
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
<div id="wrapper">
    <h1>@Brar Book Store</h1>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category ID</th>
                    <th>Books ID</th>
                    <th>Person Name</th>
                    <th>Review</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $comment) : ?>
                    <tr>
                        <td><?= $comment['id']; ?></td>
                        <td><?= $comment['category_id']; ?></td>
                        <td><?= $comment['books_id']; ?></td>
                        <td><?= $comment['personname']; ?></td>
                        <td><?= $comment['review']; ?></td>
                        <td><?= $comment['created_at']; ?></td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="comment_id" value="<?= $comment['id']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit">Delete</button>
                                <!-- Add other buttons for hide and disemvowel actions -->
                                <!-- <button type="submit" name="action" value="hide">Hide</button>
                                <button type="submit" name="action" value="disemvowel">Disemvowel</button> -->
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table><br>
        <form method="get" action="<?= $about ?>">
        <button type="submit">Back</button>
    </form>
    </div>
</body>
</html>
