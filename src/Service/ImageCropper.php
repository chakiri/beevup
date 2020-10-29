<?php


namespace App\Service;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ContainerInterface;


class ImageCropper
{
    private $container; // <- Add this
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function move_directory($entity, $uploadDirectory){

        if( isset($_FILES['file'])) {
            $file = $_FILES['file'];
            $file = new UploadedFile($file['tmp_name'], $file['name'], $file['type']);
            $filename = $this->generateUniqueName() . '.' . $file->guessExtension();
            $file->move(
                $this->container->getParameter($uploadDirectory),
                $filename
            );

            $entity->setCroppedImageFileName($filename);
            $entity->setFilename($filename);


        }

}

    public function generateUniqueName()
    {
        return md5(uniqid());
    }




}