<?php


namespace App\Service;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ContainerInterface;


class ImageCropper
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function move_directory($entity)
    {
        if( isset($_FILES['file'])) {
            $file = $_FILES['file'];
            $file = new UploadedFile($file['tmp_name'], $file['name'], $file['type']);
            $filename = $this->generateUniqueName() . '.' . $file->guessExtension();
            /*$file->move(
                $this->container->getParameter($uploadDirectory),
                $filename
            );*/

            $entity->setImageFile($file);
        }

    }

    public function generateUniqueName()
    {
        return md5(uniqid());
    }




}