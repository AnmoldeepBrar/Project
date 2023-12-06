<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $commenterName = filter_input(INPUT_POST, 'personname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $review = filter_input(INPUT_POST, 'review', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $captchaInput = filter_input(INPUT_POST, 'captcha', FILTER_SANITIZE_STRING);

    // Check if the CAPTCHA value matches the one stored in the session
    if ($captchaInput !== $_SESSION['captcha']) {
        echo "Incorrect CAPTCHA, please try again.";
    } else {
        // CAPTCHA matched, process the comment
        // ... your comment insertion logic here ...
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

        // Clear the CAPTCHA value from the session
        unset($_SESSION['captcha']);
    }
}

// Generate a random CAPTCHA string
$captcha = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 5);

// Store the CAPTCHA string in the session
$_SESSION['captcha'] = $captcha;

// Display the CAPTCHA image
$captchaImage = imagecreatetruecolor(100, 30);
$bgColor = imagecolorallocate($captchaImage, 255, 255, 255);
$textColor = imagecolorallocate($captchaImage, 0, 0, 0);
imagefilledrectangle($captchaImage, 0, 0, 100, 30, $bgColor);
imagestring($captchaImage, 5, 20, 8, $captcha, $textColor);

// Output the image
header('Content-type: image/png');
imagepng($captchaImage);
imagedestroy($captchaImage);
?>
<form method="post" action="fulldetails.php?book_id=<?= $id; ?>">
    <!-- Other form fields -->
    <!-- ... -->

    <!-- CAPTCHA input field -->
    <label for="captcha">Enter CAPTCHA:</label>
    <input type="text" id="captcha" name="captcha" placeholder="Enter CAPTCHA value">

    <!-- Submit button -->
    <button type="submit">Submit Comment</button>
</form>
