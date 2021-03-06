<?php


namespace App\Controller\Admin\Publicity;

use App\Repository\PublicityRepository;
use App\Service\ImageCropper;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class PublicityController extends EasyAdminController
{
    private $pubRepository;
    private $manager;
    public function __construct(PublicityRepository $publicityRepository, EntityManagerInterface $manager)
    {

        $this->pubRepository = $publicityRepository;
        $this->manager = $manager;

    }

    public function persistPublicityEntity($publicity)
    {
        parent::persistEntity($publicity);
    }

    /**
     * @Route("/admin/updatePublicity/{id}", name="updatePublicity")
     */
    public function editPublicity(Request $request, $id, ImageCropper $imageCropper )
    {
        if ($request->isXmlHttpRequest()){
            $publicity = $this->pubRepository->findOneBy(['id'=>$id]);
            $imageCropper->move_directory( $publicity);
            $publicity->setLink( $request->request->get('publicity')['link']);
            $this->manager->persist($publicity);
            $this->manager->flush();
            return new JsonResponse( array(
                'message' => 'vos modifications ont bien été enregistrées',

            ));
        } else {
            return $this->render('bundles/TwigBundle/Exception/error404.html.twig');
        }


    }



}