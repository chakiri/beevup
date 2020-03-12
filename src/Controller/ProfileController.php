<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Profile;
use App\Form\ProfileType;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function index()
    {
        //$this->repository
        return $this->render('profile/home.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }
    /**
     * @Route("/edit_profile/{id}", name="update_profile")
     */
    public function edit(Profile $profile, EntityManagerInterface $manager, Request $request)
    {
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
           $manager->persist($profile);
           $manager->flush();

        }
        return $this->render('profile/edit.html.twig', [
            'EditProfileForm' => $form->createView(),
        ]);
    }
}
