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
            //dump('file existe');

            $file = new UploadedFile($_FILES['file']['tmp_name'], $_FILES['file']['name'], $_FILES['file']['type']);
            //dump($file);
            $entity->setImageFile($file);
        }
        if( isset($_FILES['file1'])) {
            //dump('file1 existe');

            $file1 = new UploadedFile($_FILES['file1']['tmp_name'], $_FILES['file1']['name'], $_FILES['file1']['type']);
           // dump($file1);
            $entity->setImageFile1($file1);
        }
        if( isset($_FILES['file2'])) {
           // dump('file2 existe');
            $file2 = new UploadedFile($_FILES['file2']['tmp_name'], $_FILES['file2']['name'], $_FILES['file2']['type']);
            $entity->setImageFile2($file2);
        }
        if( isset($_FILES['file3'])) {
          //  dump('file3 existe');
            $file3 = new UploadedFile($_FILES['file3']['tmp_name'], $_FILES['file3']['name'], $_FILES['file3']['type']);
            $entity->setImageFile3($file3);
        }

    }


}