<?php
/*******w******** 
    Name: Anmoldeep Kaur
    Date: 25 September 2023
    Description: This is my first website.
****************/

require('connect.php');
require('authenticate.php');

$insert = "create.php";
$view = "view.php";
$title = "fulldetails.php";

$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'posttime';
$sortOrder = isset($_GET['order']) && strtoupper($_GET['order']) === 'DESC' ? 'DESC' : 'ASC';

$validSortColumns = ['title', 'author', 'totalcopies', 'posttime']; // Add more if needed

if (!in_array($sortBy, $validSortColumns)) {
    $sortBy = 'posttime'; // Default to 'posttime' if an invalid column is provided
}

$query = "SELECT * FROM `books` ORDER BY ";
if ($sortBy === 'title' || $sortBy === 'author') {
    $query .= "$sortBy $sortOrder, posttime DESC";
} elseif ($sortBy === 'totalcopies') {
    $query .= "$sortBy DESC, posttime DESC";
} else {
    $query .= "posttime DESC";
}

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
        <li><a href="<?= $view; ?>">See your full Catalogue</a></li>
        <li><a href="<?= $insert; ?>">Add book to your Collection</a></li> 
    </ul><br>
    <div id="sort-links">
        <p>Sort By: 
            <a href="?sort=title&order=<?= $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>">Title</a>, 
            <a href="?sort=author&order=<?= $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>">Author</a>, 
            <a href="?sort=totalcopies&order=DESC">Total Copies</a>,
            <a href="?sort=posttime&order=DESC">Latest Updated</a>
        </p>
    </div>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Image</th>
                    <th>Description</th>
                    <th>Available Copies</th>
                    <th>Total Copies</th>
                    <th>Last Update</th>
                    <th>Explore</th>
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
                        <td><?= truncateContent($book['description']); ?></td>
                        <td><?= $book['avaliablecopies']; ?></td>
                        <td><?= $book['totalcopies']; ?></td>
                        <td><?php if (isset($book['posttime'])) : ?>
                            <?php
                            $datetime = strtotime($book['posttime']);
                            echo date('F j, Y h:i a', $datetime);
                            ?>
                        <?php endif; ?></td>
                        <td><a href="fulldetails.php?book_id=<?= $book['id']; ?>">View More</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
