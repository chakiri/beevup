<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Form\SecurityType;
use App\Entity\User;
use App\Entity\Company;
use App\Entity\Profile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_inscription")
     */
    public function inscription(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();

        $form = $this->createForm(SecurityType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $company = new Company();

            /* insert company data*/
            $company->setSiret($form->get('siret')->getData());
            $company->setName($form->get('name')->getData());
            $company->setEmail($user->getEmail());
            $company->setStore($form->get('store')->getData());
            $company->setIsValid(0);

            $manager->persist($company);
            
            $roles[] = 'ROLE_USER';

            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            
            /* insert user data*/
            $user->setRoles($roles);
            $user->setCreatedAt(new \Datetime());
            $user->setIsValid(0);
            $user->setStore($form->get('store')->getData());
            $user->setCompany($company);
            $user->setPassword($password);

            $manager->persist($user);
            $profil = new Profile();
            $profil->setUser($user);
            $manager->persist($profil);

            $manager->flush();
            


            $this->addFlash('Success', 'Votre compte a été bien créer');

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
