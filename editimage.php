<?php
/*******w******** 
    
    Name: Anmoldeep Kaur 
    Date: 5 November 2023
    Description: Uploading and resizing images.

****************/

require('connect.php');

if (!empty($_GET['book_id'])) {
    $book_id = filter_input(INPUT_GET, 'book_id', FILTER_VALIDATE_INT);

    // Fetch the current book's image path
    $queryFetchImage = "SELECT image FROM books WHERE id = :book_id";
    $statementFetchImage = $db->prepare($queryFetchImage);
    $statementFetchImage->bindValue(':book_id', $book_id, PDO::PARAM_INT);
    $statementFetchImage->execute();
    $currentImagePath = $statementFetchImage->fetchColumn();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Check if the delete checkbox is checked and the current image exists
        if (isset($_POST['delete']) && $currentImagePath) {
            // Remove the image from the filesystem and database
            if (file_exists($currentImagePath)) {
                unlink($currentImagePath);
            }

            // Update the database to remove the image path
            $queryUpdateImage = "UPDATE books SET image = NULL WHERE id = :book_id";
            $statementUpdateImage = $db->prepare($queryUpdateImage);
            $statementUpdateImage->bindValue(':book_id', $book_id, PDO::PARAM_INT);
            $statementUpdateImage->execute();

            // Redirect to the same page after successful deletion
            header("Location: editimage.php?book_id=$book_id&success=1");
            exit;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $upload_dir = 'uploads';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir);
    }

    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];

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
            
            // Database interaction to store paths to resized images in the books table
            try {
                $book_id = filter_input(INPUT_GET, 'book_id', FILTER_VALIDATE_INT);

                // Update the database to set the new image path
                $queryUpdateImage = "UPDATE books SET image = :image WHERE id = :book_id";
                $statementUpdateImage = $db->prepare($queryUpdateImage);
                $statementUpdateImage->bindValue(':image', $thumbnailFilePath);
                $statementUpdateImage->bindValue(':book_id', $book_id, PDO::PARAM_INT);
                $statementUpdateImage->execute();

                header("Location: edit.php?id=$book_id&success=1");
                exit;
            } catch (PDOException $e) {
                echo "Database error: " . $e->getMessage();
            }
        }
    }
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
        <h1>Update the picture of the book</h1>
                <?php if ($currentImagePath): ?>
            <!-- Display delete form if the image exists -->
            <form method="post">
                <img src="<?= $currentImagePath; ?>" alt="Book Image" style="max-width: 200px;">
                <label>
                    <input type="checkbox" name="delete"> Delete Image
                </label>
                <button type="submit">Submit</button>
            </form>
        <?php else: ?>
            <!-- Display upload form if no image exists -->
            <form method="post" enctype="multipart/form-data">
                <label for="file">Select an Image or PDF:</label>
                <input type="file" name="file" id="file">
                <input type="submit" name="submit" value="Upload File">
            </form>
        <?php endif; ?>

        <?php if (!empty($_GET['success']) && $_GET['success'] == 1): ?>
            <p>Image has been deleted successfully.</p>
        <?php endif; ?>
    </div>
</body>
</html>
