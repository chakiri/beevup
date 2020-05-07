<?php

namespace App\Controller;

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
use Symfony\Component\HttpFoundation\Request;
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
    public function inscription(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder, UserTypeRepository $userTypeRepository, BarCode $barCode, CompanyRepository $companyRepo, TopicHandler $topicHandler): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
            
            /* insert company data*/
            $company = new Company();
            $userType = $userTypeRepository->findOneBy(['name'=> 'admin_entreprise']);
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

            $this->addFlash('success', 'Votre compte a bien été crée !');

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


        return $this->render("security/login.html.twig", [
            'hasError' => $error !== null,
            'lastUsername' => $lastUsername
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
     * @Route("/resetPassword/{token}", name="security_reset_password")
     */
    public function resetPassword(Request $request, string $token, UserRepository $userRepository, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted()) {

            $user = $userRepository->findOneBy(['resetToken' => $token]);

            if (!$user){
                $this->addFlash('danger', 'Token unknown');

                return $this->redirectToRoute('security_login');
            }

            $user->setResetToken(null);
            $user->setPassword($encoder->encodePassword($user,   $email = $form->getData()->getPassword()));

            $manager->persist($user);

            $manager->flush();

            $this->addFlash('success', 'Le mot de passe a été modifié');

            return $this->redirectToRoute('security_login');

        }
        return $this->render('security/resetPassword.html.twig', [
            'token' => $token,
            'forgotPasswordForm' => $form->createView(),
            'token',$token
        ]);
    }
}
