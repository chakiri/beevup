<?php


namespace App\Controller\Admin\Publicity;

use App\Entity\Publicity;
use App\Repository\PublicityRepository;
use App\Service\ImageCropper;
use App\Service\Map;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;


class PublicityController extends EasyAdminController
{
    private $pubRepository;
    private $manager;
    private $file;
    public function __construct(PublicityRepository $publicityRepository, EntityManagerInterface $manager, ImageCropper $file)
    {

        $this->pubRepository = $publicityRepository;
        $this->manager = $manager;
        $this->file = $file;
    }

   public function persistPublicityEntity($publicity)
    {
        parent::persistEntity($publicity);
    }

    /**
     * @Route("/admin/updatePublicity/{id}", name="updatePublicity")
     */
    public function editPublicity(Request $request, $id )
    {
        if ($request->isXmlHttpRequest()){
         $publicity = $this->pubRepository->findOneBy(['id'=>$id]);
         $filename = $this->file->uploadFile($this->getTargetDir('publicity_upload_folder'));
         $publicity->setFilename( $filename);
         $publicity->setLink( $request->request->get('publicity')['link']);
         $this->manager->persist($publicity);
         $this->manager->flush();
         return new JsonResponse();
         }

    }
    private function getTargetDir($targetDirectory)
    {
        return $this->getParameter($targetDirectory);
    }



}