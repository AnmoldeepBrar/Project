<?php
/**********
    Name: Anmoldeep Kaur
    Date: 25 September 2023
    Description: This is my first website.
**********/
session_start();

require('connect.php');
require('authenticate.php');

$createcategory = "createcategory.php";
$viewcategory = "viewcategory.php";
$categoryPage = "category.php";
$home = "home.php";

$isAdmin = false; // Initialize isAdmin as false

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
    // Retrieve user information from the database based on the user ID
    $userId = $_SESSION['user_id'];
    $userQuery = "SELECT * FROM `members` WHERE id = :user_id";
    $userStatement = $db->prepare($userQuery);
    $userStatement->bindValue(':user_id', $userId);
    $userStatement->execute();
    $user = $userStatement->fetch(PDO::FETCH_ASSOC);

    // Check if the user is an admin
    if ($user && $user['role'] === 'admin') {
        $isAdmin = true;
    }
}

// Fetch all categories
$query = "SELECT * FROM `category`";
$statement = $db->prepare($query);
$statement->execute();
$categories = $statement->fetchAll(PDO::FETCH_ASSOC);

// Function to check if comments are enabled
function areCommentsEnabled($category)
{
    return isset($_GET['comment']) && $_GET['comment'] == 'enable' && $category !== false;
}

if (isset($_GET['search_query']) && !empty($_GET['search_query'])) {
    // Sanitize the search query
    $searchQuery = '%' . filter_input(INPUT_GET, 'search_query', FILTER_SANITIZE_STRING) . '%';

    // Construct a query to search for categories based on the provided keyword(s)
    $searchCategoryQuery = "SELECT * FROM category WHERE type LIKE :search_query";
    $categoryStatement = $db->prepare($searchCategoryQuery);
    $categoryStatement->bindValue(':search_query', $searchQuery, PDO::PARAM_STR);
    $categoryStatement->execute();
    $foundCategories = $categoryStatement->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($foundCategories)) {
        // Fetch books related to the found categories
        $booksQuery = "SELECT * FROM books WHERE category_id IN (";
        $placeholders = array_fill(0, count($foundCategories), '?');
        $booksQuery .= implode(',', $placeholders) . ")";

        $bookIds = array_column($foundCategories, 'id');

        $booksStatement = $db->prepare($booksQuery);
        $booksStatement->execute($bookIds);
        $foundBooks = $booksStatement->fetchAll(PDO::FETCH_ASSOC);

    } 
}


if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    $query = "SELECT * FROM `category` WHERE id = :id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->execute();
    $category = $statement->fetch(PDO::FETCH_ASSOC);

    // Handle comment submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $commenterName = filter_input(INPUT_POST, 'personname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $review = filter_input(INPUT_POST, 'review', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (!empty($review)) {
            try {
                $insertQuery = "INSERT INTO `comments` (category_id, personname, review, created_at) VALUES (:category_id, :personname, :review, NOW())";
                $insertStatement = $db->prepare($insertQuery);
                $insertStatement->bindValue(':category_id', $id, PDO::PARAM_INT);
                $insertStatement->bindValue(':personname', $commenterName);
                $insertStatement->bindValue(':review', $review);
                $insertStatement->execute();
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
    }

    // Retrieve comments for the category
    $commentsQuery = "SELECT * FROM `comments` WHERE category_id = :category_id ORDER BY created_at DESC";
    $commentsStatement = $db->prepare($commentsQuery);
    $commentsStatement->bindValue(':category_id', $id, PDO::PARAM_INT);
    $commentsStatement->execute();
    $comments = $commentsStatement->fetchAll(PDO::FETCH_ASSOC);
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
        <li><a href="<?= $home; ?>">Home Page</a></li>
        <li><a href="<?= $categoryPage; ?>">See your full Catalogue</a></li>
        <?php foreach ($categories as $cat) : ?>
            <!-- Add links to navigate to different category types with descriptions -->
            <li>
                <a href="<?= $viewcategory; ?>?id=<?= $cat['id']; ?>">
                    <?= $cat['type']; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    <div id="wrapper">
    <form method="get" action="<?= $home; ?>">
        <input type="text" name="search_query" placeholder="Search category...">
        <button type="submit">Search</button>
        </form>
        <?php
        if (isset($_GET['search_query']) && !empty($_GET['search_query'])) {
            // Display found categories
            if (!empty($foundCategories)) {
                echo "<h3>Found Categories:</h3>";
                foreach ($foundCategories as $category) {
                    echo "<li><a href='viewcategory.php?id={$category['id']}'>{$category['type']}</a></li>";
                }
            } else {
                echo "<p>No categories found matching the search.</p>";
            }

            // Display found books
            if (!empty($foundBooks)) {
                echo "<h3>Found Books in Related Categories:</h3>";
                foreach ($foundBooks as $book) {
                    echo "<li><a href='fulldetails.php?book_id={$book['id']}'>{$book['title']}</a></li>";
                    // Display other book details as needed
                }
            } else {
                echo "<p>No books found in related categories matching the search.</p>";
            }
        }
        ?>
        <?php if (isset($category)) : ?>
            <h3><?= $category['type']; ?></h3>
            <p><?= $category['description']; ?></p>
            <!-- ... (previous code) ... -->
    <?php if ($isAdmin) : ?>
        <form method="get" action="editcategory.php">
            <input type="hidden" name="id" value="<?= $category['id']; ?>">
            <input type="hidden" name="edit">
            <button type="submit">Edit</button>
        </form>
    <?php endif; ?>
        <form method="post" action="<?= $viewcategory; ?>">
            <button type="submit">Back</button>
        </form> 

        <?php else : ?>
            <p>Category is not available.</p>
        <?php endif; ?>      
</body>
</html>
