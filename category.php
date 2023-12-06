<?php
/********** 
    Name: Anmoldeep Kaur
    Date: 25 September 2023
    Description: This is my first website.
**********/

require('connect.php');

$createcategory = "createcategory.php";  
$viewcategory = "viewcategory.php"; 
$categoryPage = "category.php"; 
$home = "home.php";
$collection ="collection.php";
$login = "login.php";

// Retrieve all categories
$query = "SELECT * FROM `category`";
$statement = $db->prepare($query);
$statement->execute();
$categories = $statement->fetchAll(PDO::FETCH_ASSOC);

// Check if a category is selected
if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    // Retrieve the selected category
    $categoryQuery = "SELECT * FROM `category` WHERE id = :id";
    $categoryStatement = $db->prepare($categoryQuery);
    $categoryStatement->bindValue(':id', $id, PDO::PARAM_INT);
    $categoryStatement->execute();
    $category = $categoryStatement->fetch(PDO::FETCH_ASSOC);

    // Retrieve books for the selected category
    $booksQuery = "SELECT * FROM `books` WHERE category_id = :category_id";
    $booksStatement = $db->prepare($booksQuery);
    $booksStatement->bindValue(':category_id', $id, PDO::PARAM_INT);
    $booksStatement->execute();
    $books = $booksStatement->fetchAll(PDO::FETCH_ASSOC);
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
        <?php if (!isset($category)) : ?>
            <!-- Display the "Categories" link only on the main page -->
            <li><a href="<?= $categoryPage; ?>">Categories</a></li>
        <?php endif; ?>
        <li><a href="<?= $collection; ?>">Books</a></li>
        <li><a href="<?= $login; ?>">Login</a></li>
    </ul>
        <?php if (!isset($category)) : ?>
            <!-- Display the list of categories -->
            <?php foreach ($categories as $categoryItem) : ?>
                <div class="category-box">
                    <h3>
                        <a href="<?= $categoryPage; ?>?id=<?= $categoryItem['id']; ?>">
                            <?= $categoryItem['type']; ?>
                        </a>
                        <a href="<?= $viewcategory; ?>?id=<?= $categoryItem['id']; ?>">
                            (View More)
                        </a>
                    </h3>
                </div>
            <?php endforeach; ?>
            <form method="post" action="<?= $home; ?>">
                <button type="submit">Back</button>
            </form>

        <?php elseif (isset($category) && isset($books)) : ?>
            <!-- Display books for the selected category -->
            <div class="category-box">
                <h3><?= $category['type']; ?></h3>
                <h4>Books in this Category:</h4>
                <ul>
                <?php foreach ($books as $book) : ?>
                    <li>
                        <!-- Convert book names to links -->
                        <a href="fulldetails.php?book_id=<?= $book['id']; ?>">
                           <?= $book['title']; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
            <!-- Display the "Next" button only when a category is selected -->
            <form method="post" action="<?= $categoryPage; ?>">
                <button type="submit">Next</button>
            </form>
        <?php else : ?>
            <!-- Display the "Back" button on the home page -->
            <p>Category is not available.</p>
        <?php endif; ?>
    </div>
</body>
</html>
