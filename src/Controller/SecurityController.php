<?php

namespace App\Controller;

use App\Security\LoginFormAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use App\Form\ForgotPasswordType;
use App\Form\ResetPasswordType;
use App\Form\ServiceType;
use App\Repository\CompanyRepository;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use App\Repository\UserTypeRepository;
use App\Service\TopicHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;
use App\Form\RegistrationType;
use App\Entity\User;
use App\Entity\Company;
use App\Entity\Profile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Service\BarCode;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;





class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function inscription(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder, UserTypeRepository $userTypeRepository, BarCode $barCode, CompanyRepository $companyRepo,   TopicHandler $topicHandler, TokenGeneratorInterface $tokenGenerator, \Swift_Mailer $mailer): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /* insert company data*/
            $company = new Company();
            $userType = $userTypeRepository->findOneBy(['id'=> 3]);
            $company->setSiret($form->get('company')->getData()->getSiret());
            $company->setName($form->get('name')->getData());
            $company->setEmail($user->getEmail());
            $company->setStore($user->getStore());

            /* generate bar code*/
            $companyId =  $companyRepo->findOneBy([],['id' => 'desc'])->getId() + 1;
            $company->setBarCode($barCode->generate( $companyId));
            /* end ******/

            $manager->persist($company);

            /* insert user data*/
            $user->setStore($user->getStore());
            $user->setCompany($company);
            $user->setType($userType);
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $token = $tokenGenerator->generateToken();
            $user->setResetToken($token);
            $url = $this->generateUrl('security_confirm_email', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
            $message = (new \Swift_Message())
                ->setSubject('Confirmation email')
                ->setFrom($_ENV['DEFAULT_EMAIL'])
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/confirmEmail.html.twig',
                        ['url' => $url,'user'=> $user]
                    ),
                    'text/html'
                )
            ;
            
            /* add admin topics to user */
            $topicHandler->addAdminTopicsToUser($user);

            $manager->persist($user);

            /* add company topic to user */
            $topicHandler->initCompanyTopic($company, $user);

            // new profile
            $profile = new Profile();
            $profile->setUser($user);

            $manager->persist($profile);

            $manager->flush();
            $result = $mailer->send($message);
            $this->addFlash('success', 'Votre compte a bien été crée. Veuillez confirmer votre adresse email en vous rendant sur le lien envoyé');

            return $this->redirectToRoute('security_login');
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
        $isActivated = true;
        if (strpos($error, 'Account disabled') !== false) {
            $isActivated= false;
        }

        return $this->render("security/login.html.twig", [
            'hasError' => $error !== null,
            'lastUsername' => $lastUsername,
            'isActivated'=>$isActivated
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout(){}

    /**
     * @Route("/forgottenPassword", name="security_forgotten_password")
     */
    public function forgottenPassword(Request $request, EntityManagerInterface $manager, UserRepository $userRepository,  TokenGeneratorInterface $tokenGenerator, \Swift_Mailer $mailer)
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() ) {
            $email = $form->getData()->getEmail();
            $user = $userRepository->findOneBy(['email' => $email]);

            if (!$user){
                $this->addFlash('danger', 'Email n\'existe pas');

                return $this->redirectToRoute('security_forgotten_password');
            }

            $token = $tokenGenerator->generateToken();
            $user->setResetToken($token);
            $manager->persist($user);
            $manager->flush();

            $url = $this->generateUrl('security_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
            $message = (new \Swift_Message())
                ->setSubject('Demande de réinitialisation de mot de passe')
                ->setFrom($_ENV['DEFAULT_EMAIL'])
                ->setTo($email)
                ->setBody(
                    $this->renderView(
                        'emails/forgotPassword.html.twig',
                        ['url' => $url,'user'=> $user]
                    ),
                    'text/html'
                   )
            ;
            $result = $mailer->send($message);


            $this->addFlash('success', 'Nous avons envoyé un email à votre adresse email. Cliquez sur le lien figurant dans cet email pour réinitialiser votre mot de passe.
                           Si vous ne voyez pas l\'email, vérifiez les autres endroits où il pourrait être, comme votre courrier indésirable, spam, social, ou autres dossiers.');


            return $this->redirectToRoute('security_forgotten_password');

        }



        return $this->render('security/forgottenPassword.html.twig', [
            'forgotPasswordForm' => $form->createView()
]);
    }

    /**
     * @Route("/newAccount/{token}", name="security_new_account")
     * @Route("/resetPassword/{token}", name="security_reset_password")
     */
    public function resetPassword(Request $request, string $token, UserRepository $userRepository, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, LoginFormAuthenticator $authenticator, GuardAuthenticatorHandler $guardHandler)
    {
       $isNewAccount =  $request->get('_route') == 'security_new_account' ? true :false ;
       $action =  $request->get('_route') == 'security_new_account' ? $this->generateUrl('security_new_account', ['token' => $token]) : $this->generateUrl('security_reset_password', ['token' => $token]);



        $form = $this->createForm(ResetPasswordType::class,null, [
            'action' => $action,
            'method' => 'post',
        ]);
        $form->handleRequest($request);


        if($form->isSubmitted()) {

            $user = $userRepository->findOneBy(['resetToken' => $token]);

            if (!$user){
                $this->addFlash('danger', 'le lien de confirmation a expiré');

                return $this->redirectToRoute('security_login');
            }

            $user->setResetToken(null);
            $user->setPassword($encoder->encodePassword($user,   $email = $form->getData()->getPassword()));
            if($isNewAccount) {
                $user->setIsValid(1) ;
            }
            $manager->persist($user);

            $manager->flush();

           if($user->isValid()) {
               $guardHandler->authenticateUserAndHandleSuccess(
                   $user,
                   $request,
                   $authenticator,
                   'main'
               );
           }


            if ($request->get('_route')=='security_new_account') {

                $this->addFlash('success', 'Bienvenu à Beeveup');
            } else {
                $this->addFlash('success', 'Le mot de passe a été modifié');
            }

            return $this->redirectToRoute('dashboard');




        }
        return $this->render('security/resetPassword.html.twig', [
            'token' => $token,
            'forgotPasswordForm' => $form->createView(),
            'token',$token
        ]);
    }

    /**
     * @Route("/confirmEmail/{token}", name="security_confirm_email")
     */
    public function confirmEmail(LoginFormAuthenticator $authenticator, Request $request, string $token, UserRepository $userRepository, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder , GuardAuthenticatorHandler $guardHandler)
    {

       $user = $userRepository->findOneBy(['resetToken' => $token]);

            if (!$user){
                $this->addFlash('danger', 'le lien de confirmation a expiré');
                return $this->redirectToRoute('security_login');
            }

            $user->setResetToken(null);
             $user->setIsValid(1);
            $manager->persist($user);
            $manager->flush();



         $guardHandler->authenticateUserAndHandleSuccess(
            $user,
            $request,
            $authenticator,
            'main'
        );
        $this->addFlash('success', 'votre compte a été activé');
        return $this->redirectToRoute('dashboard');

    }

}
