<?php

class ImageResize
{
    private $image;

    public function __construct($imagePath)
    {
        if (!file_exists($imagePath)) {
            throw new Exception('Image file not found.');
        }

        $info = getimagesize($imagePath);
        if ($info === false) {
            throw new Exception('Invalid image format or file.');
        }

        $mime = $info['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $this->image = imagecreatefromjpeg($imagePath);
                break;

            case 'image/png':
                $this->image = imagecreatefrompng($imagePath);
                break;

            case 'image/gif':
                $this->image = imagecreatefromgif($imagePath);
                break;

            default:
                throw new Exception('Invalid image type.');
        }
    }

    public function resizeToSquare($size)
    {
        $originalWidth = imagesx($this->image);
        $originalHeight = imagesy($this->image);
        
        $newWidth = $size;
        $newHeight = $size;
        
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        
        imagecopyresampled($resizedImage, $this->image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
        
        $this->image = $resizedImage;
    }

    public function resizeToWidth($width)
    {
        $originalWidth = imagesx($this->image);
        $originalHeight = imagesy($this->image);

        $newWidth = $width;
        $newHeight = ($originalHeight / $originalWidth) * $newWidth;

        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

        imagecopyresampled($resizedImage, $this->image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

        $this->image = $resizedImage;
    }

    public function save($outputPath, $quality = 80)
    {
        $extension = pathinfo($outputPath, PATHINFO_EXTENSION);

        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($this->image, $outputPath, $quality);
                break;

            case 'png':
                imagepng($this->image, $outputPath);
                break;

            case 'gif':
                imagegif($this->image, $outputPath);
                break;

            default:
                throw new Exception('Invalid output format.');
        }
    }
}
?>
