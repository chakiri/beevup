<?php

namespace App\Entity\Traits;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;


trait SeveralFiles
{

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filename1;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="service_image", fileNameProperty = "filename1")
     */
    private $imageFile1;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filename2;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="service_image", fileNameProperty = "filename2")
     */
    private $imageFile2;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filename3;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="service_image", fileNameProperty = "filename3")
     */
    private $imageFile3;

    /**
     * @return null|string
     */
    public function getFilename1()
    {
        return $this->filename1;
    }

    /**
     * @param null|string $filename1
     */
    public function setFilename1($filename1)
    {
        $this->filename1 = $filename1;
    }

    /**
     * @return File|null
     */
    public function getImageFile1()
    {
        return $this->imageFile1;
    }

    /**
     * @param File|null $imageFile1
     * @return $this
     */
    public function setImageFile1($imageFile1)
    {
        $this->imageFile1 = $imageFile1;

        if ($this->imageFile1 instanceof UploadedFile) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    /**
     * @return null|string
     */
    public function getFilename2()
    {
        return $this->filename2;
    }

    /**
     * @param File|null $filename2
     */
    public function setFilename2($filename2)
    {
        $this->filename2 = $filename2;
    }

    /**
     * @return File|null
     */
    public function getImageFile2()
    {
        return $this->imageFile2;
    }

    /**
     * @param File|null $imageFile2
     * @return $this
     */
    public function setImageFile2($imageFile2)
    {
        $this->imageFile2 = $imageFile2;

        if ($this->imageFile2 instanceof UploadedFile) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    /**
     * @return null|string
     */
    public function getFilename3()
    {
        return $this->filename3;
    }

    /**
     * @param null|string $filename3
     */
    public function setFilename3($filename3)
    {
        $this->filename3 = $filename3;
    }

    /**
     * @return File|null
     */
    public function getImageFile3()
    {
        return $this->imageFile3;
    }

    /**
     * @param File|null $imageFile3
     * @return $this
     */
    public function setImageFile3($imageFile3)
    {
        $this->imageFile3 = $imageFile3;

        if ($this->imageFile3 instanceof UploadedFile) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }


}