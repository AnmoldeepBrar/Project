<?php
/*******w******** 
    Name: Anmoldeep Kaur
    Date: 25 September 2023
    Description: This is my first website.
****************/

require('connect.php');

//$insert = "create.php";
//$view = "view.php";
$title = "fulldetails.php";
$view = "home.php";
$categoryPage = "category.php";
$list = "collection.php";

$query = "SELECT * FROM `books` ORDER BY posttime DESC";
$statement = $db->prepare($query);
$statement->execute();
$books = $statement->fetchAll(PDO::FETCH_ASSOC);

function truncateContent($content, $maxLength = 200) {
    if (strlen($content) > $maxLength) {
        $content = substr($content, 0, $maxLength) . "...";
    }
    return $content;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE,edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>@Brar Book Store</title>
</head>
<body>
<div id="wrapper">
    <h1>@Brar Book Store: Online Library</h1>
    <ul id="links">
        <li><a href="<?= $view; ?>">Home Page</a></li>
        <li><a href="<?= $categoryPage; ?>">Categories</a></li>
        <li><a href="<?= $collection; ?>">Books</a></li>
    </ul>
    <table border="2">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Image</th>
                    <th>Available Copies</th>
                    <th>Total Copies</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book) : ?>
                    <tr>
                        <td><a href="fulldetails.php?book_id=<?= $book['id']; ?>"><?= $book['title']; ?></a></td>
                        <td><?= $book['author']; ?></td>
                        <td>
                            <?php if ($book['image']): ?>
                                <img src="<?= $book['image']; ?>" alt="<?= $book['title']; ?>" style="max-width: 150px; max-height: 150px;">
                                <?php else: ?>
                                     No Image Available
                                <?php endif; ?>
                        </td>
                        <td><?= $book['avaliablecopies']; ?></td>
                        <td><?= $book['totalcopies']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
