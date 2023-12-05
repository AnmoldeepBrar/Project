<?php
/******w******* 
    
    Name: Anmoldeep Kaur
    Date: 25 September 2023
    Description: This is my first website.

****************/

require('connect.php');
require('authenticate.php');

$insert = "create.php";  
$view = "view.php";    

$_SESSION["errors"] = ""; 
$posttime = date('Y-m-d H:i:s', time());

$query = "SELECT id, type FROM category";
$statement = $db->prepare($query);
$statement->execute();
$categories = $statement->fetchAll(PDO::FETCH_ASSOC);
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $avaliablecopies = filter_input(INPUT_POST, 'avaliable', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $totalcopies = filter_input(INPUT_POST, 'total', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $category_id = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);

    if (empty($title)) {
        $_SESSION["errors"] = "Title is required. Please enter the title for the book.";
    }
    if (empty($author)) {
        $_SESSION["errors"] = "Author is required. Please enter the author's name.";
    }
    if (empty($description)) {
        $_SESSION["errors"] = "Description is required. Please add a few sentences about the book.";
    }
    if (empty($avaliablecopies)) {
        $_SESSION["errors"] = "Please add the number of books available.";
    }
    if (empty($totalcopies)) {
        $_SESSION["errors"] = "Please specify the total number of books.";
    }
    if (empty($category_id)) {
        $_SESSION["errors"] = "Please select the type of category it belongs to.";
    }

    if (empty($_SESSION["errors"])) {
        try {
            $query = "INSERT INTO books (title, author, description, avaliablecopies, totalcopies, posttime, category_id) VALUES (:title, :author, :description, :avaliablecopies, :totalcopies, :posttime, :category_id)";
            $statement = $db->prepare($query);
            $statement->bindValue(':title', $title);
            $statement->bindValue(':author', $author);
            $statement->bindValue(':description', $description);
            $statement->bindValue(':avaliablecopies', $avaliablecopies);
            $statement->bindValue(':totalcopies', $totalcopies);
            $statement->bindValue(':posttime', $posttime);
            $statement->bindValue(':category_id', $category_id, PDO::PARAM_INT);
            $statement->execute();

            $book_id = $db->lastInsertId();

            $upload_dir = 'uploads';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir);
            }

            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif'];

            if (!empty($_FILES['file']['name'])) {
                $file_extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                $file_mime_type = mime_content_type($_FILES['file']['tmp_name']);

                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif'];
    
                if (!empty($_FILES['file']['name'])) {
                    $file_extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                    $file_mime_type = mime_content_type($_FILES['file']['tmp_name']);
    
                    if (
                        in_array($file_extension, $allowed_extensions)
                        && in_array($file_mime_type, $allowed_mime_types)
                    ) {
                        $new_file_path = "$upload_dir/$book_id.$file_extension";
                        move_uploaded_file($_FILES['file']['tmp_name'], $new_file_path);
    
                        // Resize image if it's an acceptable format (e.g., jpg, jpeg, png, gif)
                        if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                            require_once 'ImageResize.php';

                            $image = new ImageResize($new_file_path);
                            $image->resizeToSquare(200); // Example: Resizing to a square of 200x200
                            $image->save($new_file_path); // Overwrite the original image with the resized one
                        }
    
                        $query = "UPDATE books SET image = :image WHERE id = :book_id";
                        $statement = $db->prepare($query);
                        $statement->bindValue(':image', $new_file_path);
                        $statement->bindValue(':book_id', $book_id, PDO::PARAM_INT);
                        $statement->execute();
                    } else {
                        $query = "DELETE FROM books WHERE id = :book_id";
                        $statement = $db->prepare($query);
                        $statement->bindValue(':book_id', $book_id, PDO::PARAM_INT);
                        $statement->execute();
    
                        $_SESSION["errors"] = "Invalid file format. Please upload an image.";
                    }
                }
            }

            if (empty($_SESSION["errors"])) {
                header("Location: view.php");
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION["errors"] = "Database error: " . $e->getMessage();
        }
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
    <h1>@Brar Book Store: Online Library</h1>
    <?php if (!empty($_SESSION["errors"])): ?>
        <div class="error">
            <ul>
                
                    <div id="error"><?= $_SESSION["errors"]; session_unset();?></div>
                
            </ul>
        </div>
    <?php else: ?>
        <ul id="links">
            <li><a href="<?= $view; ?>">See your full Catalogue</a></li>
            <li><a href="<?= $insert; ?>">Add book to your Collection</a></li> 
        </ul>
        <p>Add book to your Collection</p>
        <div id="wrapper">
            <form method="post" action="" enctype="multipart/form-data">
                <label for="title">Title</label>
                <input id="title" name="title"><br>
                <label for="author">Author</label>
                <input id="author" name="author"><br>
                <label for="category">Category</label>
                <select id="category" name="category">
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id']; ?>"><?= $category['type']; ?></option>
                    <?php endforeach; ?>
                </select><br><br>
                <label for="file">Upload Book Image:</label>
                <input type="file" name="file" id="file">
                <input type="submit" name="submit" value="Upload File">
                <?php if (!empty($_GET['success']) && $_GET['success'] == 1): ?>
                    <div id="success_message">
                        File has been uploaded and processed successfully.
                    </div>
                <?php endif; ?>
                <label for="description">Description</label>
                <textarea id="description" name="description"></textarea><br>
                <label for="avaliable">Available Copies</label>
                <input id="avaliable" name="avaliable"><br>
                <label for="total">Total Copies</label>
                <input id="total" name="total"><br>
                <button>Create</button>
            </form>
        </div>
    <?php endif; ?>
</body>
</html>
