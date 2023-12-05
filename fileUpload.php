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

$errors = [];
$posttime = date('Y-m-d H:i:s', time());

$query = "SELECT id, type FROM category";
$statement = $db->prepare($query);
$statement->execute();
$categories = $statement->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $avaliablecopies = filter_input(INPUT_POST, 'avaliable', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $totalcopies = filter_input(INPUT_POST, 'total', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $category_id = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);

    if (empty($title)) {
        $errors[] = "Title is required. Please enter the title for the book.";
    }
    if (empty($author)) {
        $errors[] = "Author is required. Please enter the author's name.";
    }
    if (empty($description)) {
        $errors[] = "Description is required. Please add a few sentences about the book.";
    }
    if (empty($avaliablecopies)) {
        $errors[] = "Please add the number of books available.";
    }
    if (empty($totalcopies)) {
        $errors[] = "Please specify the total number of books.";
    }
    if (empty($category_id)) {
        $errors[] = "Please select the type of category it belongs to.";
    }

    if (empty($errors)) {
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

            // Retrieve the ID of the last inserted book
            $book_id = $db->lastInsertId();

            // Image upload logic
            $upload_dir = 'uploads';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir);
            }

            $allowed_extensions = ['jpg', 'jpeg', 'PNG', 'gif', 'pdf'];
            $allowed_mime_types = ['image/jpeg', 'image/PNG', 'image/gif', 'application/pdf'];

            if (!empty($_FILES['file']['name'])) {
            $file_name = $_FILES['file']['name'];
            $temporary_file_path = $_FILES['file']['tmp_name'];
            $new_file_path = "$upload_dir/$file_name";

            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $file_mime_type = mime_content_type($temporary_file_path);

            if (in_array($file_extension, $allowed_extensions) && in_array($file_mime_type, $allowed_mime_types)) {
                move_uploaded_file($temporary_file_path, $new_file_path);

                if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    require_once 'ImageResize.php';

                    // Create an instance of ImageResize and resize the uploaded image
                    $image = new ImageResize($new_file_path);
                    $image->resizeToSquare(200);
                    $thumbnailFileName = "{$file_name}_thumbnail.jpg"; // Define a new name for the thumbnail version
                    $thumbnailFilePath = "$upload_dir/$thumbnailFileName";
                    $image->save($thumbnailFilePath, 80); // Save thumbnail version as JPG with quality 80

                    unlink($new_file_path);

                    // Store only the thumbnail version in the database
                    $query = "UPDATE books SET image = :image WHERE id = :book_id";
                    $statement = $db->prepare($query);
                    $statement->bindValue(':image', $thumbnailFilePath);
                    $statement->bindValue(':book_id', $book_id, PDO::PARAM_INT);
                    $statement->execute();

                $query = "UPDATE books SET image = :image WHERE id = :book_id";
                $statement = $db->prepare($query);
               // $statement->bindValue(':image', $new_file_path);
               // $statement->bindValue(':image', $new_file_path);
                $statement->bindValue(':image', $thumbnailFilePath);
                $statement->bindValue(':book_id', $book_id, PDO::PARAM_INT);
                $statement->execute();

                }
            }

                header("Location: view.php");
                exit;
            } else {
                $errors[] = "Invalid file format. Please upload an image.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
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
    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <div id="error"><?= $error; ?></div>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php else: ?>
        <ul id="links">
            <li><a href="<?= $view; ?>">See your full Catalogue</a></li>
            <li><a href="<?= $insert; ?>">Add book to your Collection</a></li> 
        </ul>
        <p>Add book to your Collection</p>
        <div id="wrapper">
            <form method="post" action="create.php" enctype="multipart/form-data">
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