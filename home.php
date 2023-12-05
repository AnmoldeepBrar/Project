<?php
/******w******* 
    Name: Anmoldeep Kaur
    Date: 25 September 2023
    Description: This is my first website.
****************/

session_start();

require('connect.php');

$home = "home.php";
$category = "category.php";
$collection = "collection.php";
$login = "signup.php";
$about = "about.php";

$isAdmin = false;

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
    // Display a welcome message for the logged-in user
    $userId = $_SESSION['user_id'];

    // Retrieve user information from the database based on the user ID
    $userQuery = "SELECT * FROM `members` WHERE id = :user_id";
    $userStatement = $db->prepare($userQuery);
    $userStatement->bindValue(':user_id', $userId);
    $userStatement->execute();
    $user = $userStatement->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $welcomeMessage = "Welcome, " . $user['name'] . "! You are successfully logged in.";
        
        // Check if the user is an admin
        if ($user['role'] === 'admin') {
            $isAdmin = true;
        } else {
            $isAdmin = false;
        }
    } else {
        $welcomeMessage = "Welcome! You are successfully logged in.";
        $isAdmin = false;
    }

}

if (isset($_GET['search_query']) && !empty($_GET['search_query'])) {
    // Sanitize the search query
    $searchQuery = '%' . filter_input(INPUT_GET, 'search_query', FILTER_SANITIZE_STRING) . '%';

    // Construct a query to search for books based on the provided keyword(s)
    $searchBooksQuery = "SELECT * FROM books 
                         WHERE title LIKE :search_query 
                         OR author LIKE :search_query ";

    $booksStatement = $db->prepare($searchBooksQuery);
    $booksStatement->bindValue(':search_query', $searchQuery, PDO::PARAM_STR);
    $booksStatement->execute();
    $foundBooks = $booksStatement->fetchAll(PDO::FETCH_ASSOC);

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
    <ul id="links">
        <li><a href="<?= $home; ?>">Home Page</a></li>
        <li><a href="<?= $category; ?>">Categories</a></li>
        <li><a href="<?= $login; ?>">Sign Up</a></li>
        <?php if ($isAdmin): ?>
            <li><a href="<?= $about; ?>">Settings</a></li>
        <?php endif; ?>
    </ul>
    <div id="wrapper">
        <h2>Welcome to @Brar Book Store</h2>
        <?php if (isset($welcomeMessage)): ?>
            <p><?= $welcomeMessage; ?></p>
        <?php endif; ?>
        <form method="get" action="<?= $home; ?>">
        <input type="text" name="search_query" placeholder="Search book by keyword...">
        <button type="submit">Search</button>
        </form>
        <?php
if (isset($_GET['search_query']) && !empty($_GET['search_query'])) {
    // Display found books
    if (!empty($foundBooks)) {
        echo "<h3>Found Books:</h3>";
        foreach ($foundBooks as $book) {
            echo "<li><a href='fulldetails.php?book_id={$book['id']}'>{$book['title']}</a></li>";
            // Display other book details as needed
        }
    } else {
        echo "<p>No books found matching the search.</p>";
    }
}
?>
        <p>Discover a wide range of books and build your collection with us.</p>
        <p>Explore our collection and find your next favorite read!</p>
    <form method="get" action="<?= $category; ?>">
        <button type="submit">Next</button>
    </form>
    </div>
</body>
</html>