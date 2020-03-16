<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Form\RegistrationType;
use App\Entity\User;
use App\Entity\Company;
use App\Entity\Profile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function inscription(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
            
            /* insert company data*/
            $company = new Company();
            $company->setSiret($form->get('company')->getData()->getSiret());
            $company->setName($form->get('name')->getData());
            $company->setEmail($user->getEmail());
            $company->setStore($user->getStore());

            $manager->persist($company);

            /* insert user data*/
            $user->setStore($user->getStore());
            $user->setCompany($company);
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $manager->persist($user);

            // new profile
            $profile = new Profile();
            $profile->setUser($user);

            $manager->persist($profile);

            $manager->flush();

            $this->addFlash('Success', 'Votre compte a bien été crée !');

            return ($this->redirectToRoute('security_login'));
        }

        return $this->render('security/registration.html.twig', [
            'RegistrationForm' => $form->createView(),
        ]);
    }


    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render("security/login.html.twig", [
            'hasError' => $error !== null,
            'lastUsername' => $lastUsername
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout(){}
}
