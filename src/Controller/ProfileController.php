<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Profile;
use App\Form\ProfileType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProfileController extends AbstractController
{
    /**
     * @Route("/myaccount/{id}", name="profile_show")
     */
    public function show(Profile $profile)
    {
        return $this->render('profile/show.html.twig', [
            'profile' => $profile,
        ]);
    }
    /**
     * @Route("/myaccount/{id}/edit", name="profile_edit")
     */
    public function edit(Profile $profile, EntityManagerInterface $manager, Request $request)
    {
        $form = $this->createForm(ProfileType::class, $profile);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['imageFile']->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                //$safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $safeFilename =  $originalFilename;
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('profil_photo'),
                        $newFilename
                    );
                    
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                   
                }
                $profile->setPhoto($newFilename);
            }
            $profile->setIsCompleted(true);

            $manager->persist($profile);
            $manager->flush();

            return $this->redirectToRoute('profile_show', [
               'id' => $profile->getId()
            ]);
        }

        return $this->render('profile/edit.html.twig', [
            'EditProfileForm' => $form->createView(),
        ]);
    }
}
