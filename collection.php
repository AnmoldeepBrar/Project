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
    <h1>@Brar Book Store: Online Library</h1>
    <ul id="links">
        <li><a href="<?= $view; ?>">Home Page</a></li>
        <li><a href="<?= $categoryPage; ?>">Categories</a></li>
        <li><a href="<?= $collection; ?>">Book List</a></li>
        <!-- <li><a href="<?= $insert; ?>">Add book to your Collection</a></li>  -->

    </ul>
    <div id="wrapper">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <!-- <th>Description</th> -->
                    <th>Available Copies</th>
                    <th>Total Copies</th>
                    <!-- <th>Last Update</th> -->
                    <!-- <th>Explore</th> -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book) : ?>
                    <tr>
                        <td><a href="fulldetails.php?book_id=<?= $book['id']; ?>"><?= $book['title']; ?></a></td>
                        <td><?= $book['author']; ?></td>
                        <!-- <td><?= truncateContent($book['description']); ?></td> -->
                        <td><?= $book['avaliablecopies']; ?></td>
                        <td><?= $book['totalcopies']; ?></td>
                        <!-- <td><?php if (isset($book['posttime'])) : ?>
                            <?php
                            $datetime = strtotime($book['posttime']);
                            echo date('F j, Y h:i a', $datetime);
                            ?>
                        <?php endif; ?></td> -->
                        <!-- <td><a href="fulldetails.php?id=<?= $book['id']; ?>">View Details</a></td> -->
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
