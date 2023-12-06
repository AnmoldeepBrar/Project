<?php
/*******w******** 
    Name: Anmoldeep Kaur
    Date: 25 September 2023
    Description: This is my first website.
****************/

require('connect.php');

$home = "home.php";
$category = "category.php";
$collection = "collection.php";
$about = "about.php";
$create = "create.php";
$createcategory = "createcategory.php";
$comment = "comment.php";
$member = "members.php";

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
        <li><a href="<?= $about; ?>">Settings</a></li>
    </ul>
    <div id="wrapper">
        <p>Settings</p>
        <ul>
            <li><a href="<?= $create; ?>">Books</a></li>
            <li><a href="<?= $createcategory; ?>">Category</a></li>
            <li><a href="<?= $comment; ?>">Comments</a></li>
            <li><a href="<?= $member; ?>">Members</a></li>
        </ul>
    <form method="get" action="<?= $home; ?>">
        <button type="submit">Back</button>
    </form>
    </div>
</body>
</html>