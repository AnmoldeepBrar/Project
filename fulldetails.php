<?php
/*******w******** 
    Name: Anmoldeep Kaur
    Date: 25 September 2023
    Description: This is my first website.
****************/

session_start();

require('connect.php');

$insert = "create.php";
$view = "home.php";
$categoryPage = "category.php";
$collection ="collection.php";
$id = null;
$details = "fulldetails.php";
$home = "home.php";
//$commentsQuery="";


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

function areCommentsEnabled($category)
{
    return isset($_GET['comment']) && $_GET['comment'] == 'enable' && $category !== false;
}

$comments = [];

if (isset($_GET['book_id'])) {
    $id = filter_input(INPUT_GET, 'book_id', FILTER_SANITIZE_NUMBER_INT);

    $query = "SELECT * FROM `books` WHERE id = :book_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':book_id', $id, PDO::PARAM_INT);
    $statement->execute();
    $book = $statement->fetch(PDO::FETCH_ASSOC);

    if ($book) {
        $formattedDate = date("F d, Y, h:i a", strtotime($book['posttime']));
    } else {
        // Book not found
        $book = false;
    }
}

// Fetch comments for the book
$commentsQuery = "SELECT * FROM `comments` WHERE books_id = :books_id ORDER BY created_at DESC";
$commentsStatement = $db->prepare($commentsQuery);
$commentsStatement->bindValue(':books_id', $id, PDO::PARAM_INT);
$commentsStatement->execute();
$comments = $commentsStatement->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission separately
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $commenterName = filter_input(INPUT_POST, 'personname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $review = filter_input(INPUT_POST, 'review', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (!empty($commenterName) && !empty($review) && isset($id)) {
        try {
            $insertQuery = "INSERT INTO `comments` (category_id, books_id, personname, review, created_at) VALUES (:category_id, :books_id, :personname, :review, NOW())";
            $insertStatement = $db->prepare($insertQuery);
            $insertStatement->bindValue(':category_id', $book['category_id'], PDO::PARAM_INT);
            $insertStatement->bindValue(':books_id', $id, PDO::PARAM_INT);
            $insertStatement->bindValue(':personname', $commenterName);
            $insertStatement->bindValue(':review', $review);
            $insertStatement->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // Refresh the comments list after submitting a new comment
    $commentsStatement->execute();
    $comments = $commentsStatement->fetchAll(PDO::FETCH_ASSOC);
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
        <li><a href="<?= $view; ?>">Home Page</a></li>
        <li><a href="<?= $categoryPage; ?>">Categories</a></li>
        <li><a href="<?= $collection; ?>">Books</a></li>
    </ul>
<!--     <form method="get" action="<?= $home; ?>">
        <input type="text" name="search_query" placeholder="Search category...">
        <button type="submit">Search</button>
        </form> -->
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
    <?php if (isset($book)) : ?>
        <h3><?= $book['title']; ?></h3>
        <table  border ="1">
            <tr>
                <th>Author</th>
                <td><?= $book['author']; ?></td>
            </tr>
            <tr>
                <th>Image</th>
                <td>
                    <?php if ($book['image']): ?>
                        <img src="<?= $book['image']; ?>" alt="<?= $book['title']; ?>" style="max-width: 150px; max-height: 150px;">
                    <?php else: ?>
                        No Image Available
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
            <?php if ($isAdmin): ?>
                <th>Description</th>
                <td><?= $book['description']; ?></td>
            <?php endif; ?>
            </tr>
            <tr>
                <th>Available Copies</th>
                <td><?= $book['avaliablecopies']; ?></td>
            </tr>
            <tr>
                <th>Total Copies</th>
                <td><?= $book['totalcopies']; ?></td>
            </tr>
            <tr>
                <th>Last Updated</th>
                <td><?= $formattedDate; ?></td>
            </tr>
        </table><br>
        <!-- Edit and Delete buttons -->
          <!-- Edit and Back buttons -->
        <?php if ($isAdmin && isset($book)) : ?>
            <!-- Edit and Back buttons -->
            <form method="get" action="edit.php">
                <input type="hidden" name="id" value="<?= $book['id']; ?>">
                <input type="hidden" name="edit">
                <button type="submit">Edit</button>
            </form><br>
        <?php endif; ?>

        <p><a href="#" onclick="toggleCommentForm(); return false;">Add your Comment here</a></p>

        <form id="comment-form" method="post" action="fulldetails.php?book_id=<?= $id; ?>">
            <input type="hidden" name="category_id" value="<?= $id; ?>">
            <input type="hidden" name="books_id" value="<?= $id; ?>">
            <label for="personname">Your Name:</label>
            <input type="text" id="personname" name="personname">

            <label for="review">Your Comment:</label>
            <textarea id="review" name="review" rows="4"></textarea>

            <button type="submit">Submit Comment</button>
        </form>

        <h4>Comments:</h4>
        <ul>
            <?php foreach ($comments as $comment) : ?>
                <li>
                    <strong><?= $comment['personname']; ?>:</strong>
                    <?= $comment['review']; ?>
                    <small><?= $comment['created_at']; ?></small>
                </li>
            <?php endforeach; ?>
            <?php if (empty($comments)) : ?>
                <li>No comments yet. Be the first to comment!</li>
            <?php endif; ?>
        </ul>

        <form method="post" action="<?= $collection; ?>">
            <button type="submit">Back</button>
        </form> 
    <?php else : ?>
        <p>Book is not available.</p>
    <?php endif; ?>
    </div>

    <script>
        // JavaScript function to toggle the comment form visibility
        function toggleCommentForm() {
            var commentForm = document.getElementById('comment-form');
            var currentDisplayStyle = window.getComputedStyle(commentForm).getPropertyValue('display');

            commentForm.style.display = (currentDisplayStyle === 'none' || currentDisplayStyle === '') ? 'block' : 'none';
        }
    </script>
</body>
</html>
