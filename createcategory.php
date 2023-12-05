<?php
/*******w******** 
    
    Name: Anmoldeep Kaur
    Date: 25 September 2023
    Description: This is my first website.

****************/

require('connect.php');
require('authenticate.php');

$createcategory = "createcategory.php";  
$listcategory = "category.php";    

$errors = [];
$posttime = date('Y-m-d H:i:s', time());

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (empty($type)) {
        $errors[] = "Category is required. Please enter the category for the books.";
    }
    if (empty($description)) {
        $errors[] = "Description is required. Please add a few sentences about the category.";
    }

    if (empty($errors)) {
        try {
            $query = "INSERT INTO `category` (type, description) VALUES (:type, :description)";
            $statement = $db->prepare($query);
            $statement->bindValue(':type', $type);
            $statement->bindValue(':description', $description);
            $statement->execute();
            header("Location: category.php");
            exit;
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
            <li><a href="<?= $listcategory; ?>">See your full Catalogue</a></li>
            <li><a href="<?= $createcategory; ?>">Add category to your Collection</a></li> 
        </ul>
        <p>Add book to your Collection</p>
        <div id="wrapper">
        <form method="post" action="createcategory.php">
            <label for="type">Ctegory</label>
            <input id="type" name="type"><br>
            <label for="description">Description</label>
            <textarea id="description" name="description"></textarea><br>
            <button>Create</button>
        </form>
        </div>
    <?php endif; ?>
</body>
</html>