<?php


namespace App\Controller;


use App\Entity\ImageGallery;
use App\Form\GallerySearchType;
use App\Repository\CommentRepository;
use App\Repository\ImageGalleryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GalleryController extends AbstractController
{
    /**
 * @Route("/gallery/images", name="gallery_show")
 * @Route("/gallery/search/{keywords}", name="gallery_search")
 */
    public function show(EntityManagerInterface $manager, ImageGalleryRepository $imageGallery, $keywords =''){
        $form = $this->createForm(GallerySearchType::class);
        if($keywords !=''){
            $gallery = $imageGallery->search($keywords);

        } else {
            $gallery = $imageGallery->findAll();
        }
        return $this->render('gallery/show.html.twig', [
            'gallery' =>  $gallery,
            'searchForm' => $form->createView(),
        ]);

    }

}