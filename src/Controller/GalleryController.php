<?php

namespace App\Controller;

use App\Form\GallerySearchType;
use App\Repository\ImageGalleryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/app")
 */
class GalleryController extends AbstractController
{
    /**
     * @Route("/gallery/images", name="gallery_show")
     * @Route("/gallery/search/{keywords}", name="gallery_search")
     */
    public function show(ImageGalleryRepository $imageGallery, $keywords = null)
    {
        $form = $this->createForm(GallerySearchType::class);

        if($keywords){
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