<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Profile;
use App\Form\ProfileType;

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
