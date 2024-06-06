<?php

declare(strict_types=1);

namespace User\Service;

use Exception;

class PictureService
{
    /**
     * Represents loaded image
     * @var resource|null
     */
    private $image;

    /**
     * Represents  the image type (IMAGE_JPEG|IMAGE_GIF|IMAGE_PNG) constant
     * @var mixed - int|null would be more precise
     */
    private mixed $imageType;

    /**
     * @param string $imagePath
     * @throws Exception
     */
    public function __construct(string $imagePath)
    {
        $this->setImage($imagePath);
    }

    /**
     * @return resource|null
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $imagePath
     * @return void
     * @throws Exception
     */
    public function setImage(string $imagePath): void
    {
        $imageInfo = getimagesize($imagePath);
        if (!$imageInfo) {
            throw new Exception("Image failed loading: $imagePath");
        }

        $this->imageType = $imageInfo[2];

        $this->image = match ($this->imageType) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($imagePath),
            IMAGETYPE_GIF => imagecreatefromgif($imagePath),
            IMAGETYPE_PNG => imagecreatefrompng($imagePath),
            default => throw new Exception("Unsupported image type: $this->imageType")
        };
    }

    /**
     * @param int $width
     * @param int $height
     * @return void
     */
    private function resize(int $width, int $height): void
    {
        $newImage = imagecreatetruecolor($width, $height);

        imagecopyresampled(
            $newImage,
            $this->image,
            0,
            0,
            0,
            0,
            $width,
            $height,
            imagesx($this->image),
            imagesy($this->image)
        );

        imagedestroy($this->image);
        $this->image = $newImage;
    }

    /**
     * @param int $width
     * @return void
     */
    public function resizeToHeight(int $width): void
    {
        $height = (int)(($width / imagesx($this->image)) * imagesy($this->image));
        $this->resize($width, $height);
    }

    /**
     * @param int $height
     * @return void
     */
    public function resizeToWidth(int $height): void
    {
        $width = (int)(($height / imagesy($this->image)) * imagesy($this->image));
        $this->resize($width, $height);
    }

    /**
     * @param string $filename
     * @param int|null $imageType
     * @param int $compression
     * @return void
     */
    public function save(string $filename, int $imageType = null, int $compression = 75): void
    {
        if (!$imageType) {
            $imageType = $this->imageType;
        }

        switch ($imageType) {
            case IMAGETYPE_JPEG:
                imagejpeg($this->image, $filename, $compression);
                break;

            case IMAGETYPE_GIF:
                imagegif($this->image, $filename);
                break;

            case IMAGETYPE_PNG:
                imagepng($this->image, $filename);
                break;
        }
    }

    /**
     * @param int $startX
     * @param int $startY
     * @param int $cropWidth
     * @param int $cropHeight
     * @return void
     */
    public function crop(int $startX, int $startY, int $cropWidth, int $cropHeight): void
    {
        $newImage = imagecreatetruecolor($cropWidth, $cropHeight);
        imagecopyresampled(
            $newImage,
            $this->image,
            0,
            0,
            $startX,
            $startY,
            $cropWidth,
            $cropHeight,
            $cropWidth,
            $cropHeight
        );
        imagedestroy($this->image);
        $this->image = $newImage;
    }
}
