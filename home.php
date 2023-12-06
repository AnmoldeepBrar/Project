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
$login = "login.php";
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
<div id="wrapper">
    <h1>@Brar Book Store</h1>
    <ul id="links">
        <li><a href="<?= $home; ?>">Home Page</a></li>
        <li><a href="<?= $category; ?>">Categories</a></li>
        <li><a href="<?= $collection; ?>">Books</a></li>
        <li><a href="<?= $login; ?>">Login</a></li>
        <?php if ($isAdmin): ?>
            <li><a href="<?= $about; ?>">Settings</a></li>
        <?php endif; ?>
    </ul>
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
        <p>Dive into a world of endless stories, knowledge, and imagination. At our library, each page turns into a new adventure, each book a gateway to discovery.
            Explore our shelves filled with tales that transport you to distant lands, learning materials that fuel your curiosity, and narratives that capture your heart. Whether you're seeking a gripping novel, educational resources, or a tranquil spot to immerse yourself in the world of words, you've found your sanctuary.
            Indulge in the joy of exploring, learning, and unwinding among the pages. Our library is not just a collection of books; it's a haven for every reader, a space to embrace the magic of literature.
            Join us in this journey of exploration and enrichment. Welcome to a place where stories come alive and curiosity knows no bounds. Welcome to our Library!"</p>
        <p>Explore our collection and find your next favorite read!</p>
    <form method="get" action="<?= $category; ?>">
        <button type="submit">Next</button>
    </form>
    </div>
</body>
</html>