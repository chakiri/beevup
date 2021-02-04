<?php

namespace App\Controller;

use App\Event\Logger\LoggerEntityEvent;
use App\Repository\ScorePointRepository;
use App\Security\LoginFormAuthenticator;
use App\Service\Chat\AutomaticMessage;
use App\Service\ContactsHandler;
use App\Service\Session\CookieAcceptedSession;
use App\Service\ScoreHandler;
use App\Service\Mailer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use App\Form\ForgotPasswordType;
use App\Form\ResetPasswordType;
use App\Repository\CompanyRepository;
use App\Repository\UserRepository;
use App\Repository\UserTypeRepository;
use App\Repository\SponsorshipRepository;
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
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;


class SecurityController extends AbstractController
{
    /**
     * @Route("/", name="security_registration")
     */
    public function inscription(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder, UserTypeRepository $userTypeRepository, BarCode $barCode, CompanyRepository $companyRepo, UserRepository $userRepository, TopicHandler $topicHandler, TokenGeneratorInterface $tokenGenerator, Mailer $mailer, ContactsHandler $contactsHandler): Response
    {
        if ($this->isGranted('ROLE_USER') == false) {
            $user = new User();

            $form = $this->createForm(RegistrationType::class, $user);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                /* insert company data*/
                $company = new Company();
                $userType = $userTypeRepository->findOneBy(['id' => 3]);
                $userTypePatron = $userTypeRepository->findOneBy(['id' => 1]);
                $storePatron = $userRepository->findOneBy(['type' => $userTypePatron, 'store' => $user->getStore(), 'isValid'=>1]);

                $company->setSiret($form->get('company')->getData()->getSiret());
                $company->setName($form->get('name')->getData());
                $company->setEmail($user->getEmail());
                $company->setStore($user->getStore());

                if ($form->get('addressNumber')->getData()) $company->setAddressNumber($form->get('addressNumber')->getData());
                if ($form->get('addressStreet')->getData()) $company->setAddresseStreet($form->get('addressStreet')->getData());
                if ($form->get('addressPostCode')->getData()) $company->setAddressPostCode($form->get('addressPostCode')->getData());
                if ($form->get('city')->getData()) $company->setCity($form->get('city')->getData());
                $company->setCountry('FR');

                /* generate bar code*/
                $companyId = $companyRepo->findOneBy([], ['id' => 'desc'])->getId() + 1;
                $company->setBarCode($barCode->generate($companyId));
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

                /* add admin topics to user */
                //$topicHandler->addAdminTopicsToUser($user);
                $topicHandler->initGeneralStoreTopic($user);

                $manager->persist($user);

                /* add company topic to user */
                $topicHandler->initCompanyTopic($company, $user);

                // new profile
                $profile = new Profile();
                $profile->setUser($user);

                $manager->persist($profile);

                $manager->flush();

                //Create new contact on SendinBlue
                $contactsHandler->handleContactSendinBlueRegistartion($user);

                $mailer->sendEmailWithTemplate($user->getEmail(), ['url' => $url], 2);

                return $this->redirectToRoute('waiting_validation');
            }

            return $this->render('default/home.html.twig', [
                'RegistrationForm' => $form->createView(),
            ]);
        }else{
            return $this->redirectToRoute('dashboard');
        }
    }

    /**
     * @Route("/waitingValidation", name="waiting_validation")
     */
    public function waitingValidation(){
        return $this->render('security/waitingValidation.html.twig');
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
    public function forgottenPassword(Request $request, EntityManagerInterface $manager, UserRepository $userRepository, TokenGeneratorInterface $tokenGenerator, UserTypeRepository $userTypeRepository, Mailer $mailer)
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() ) {
            $email = $form->getData()->getEmail();
            $user = $userRepository->findOneBy(['email' => $email]);

            if (!$user){
                $this->addFlash('danger', 'Cet email n\'existe pas');

                return $this->redirectToRoute('security_forgotten_password');
            }

            $userTypePatron = $userTypeRepository->findOneBy(['id'=> 1]);
            $storePatron = $userRepository->findOneBy(['type'=> $userTypePatron, 'store'=>$user->getStore(), 'isValid'=>1]);

            $token = $tokenGenerator->generateToken();
            $user->setResetToken($token);
            $manager->persist($user);
            $manager->flush();

            $url = $this->generateUrl('security_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
            $mailer->sendEmailWithTemplate($email, ['url' => $url], 3);


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

        $user = $userRepository->findOneBy(['resetToken' => $token]);

        if (!$user){
            $this->addFlash('danger', 'le lien de confirmation a expiré');

            return $this->redirectToRoute('security_login');
        }

        $form = $this->createForm(ResetPasswordType::class,$user, [
            'action' => $action,
            'method' => 'post',
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user->setResetToken(null);
            $user->setPassword($encoder->encodePassword($user, $form->getData()->getPassword()));
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
                $this->addFlash('success', 'Bienvenue à Beev\'Up ');
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
    public function confirmEmail(LoginFormAuthenticator $authenticator, Request $request, string $token, UserRepository $userRepository, UserTypeRepository $userTypeRepository, CompanyRepository $companyRepository, EntityManagerInterface $manager, GuardAuthenticatorHandler $guardHandler, SponsorshipRepository $sponsorshipRepository, ScoreHandler $scoreHandler, ScorePointRepository $scorePointRepository, AutomaticMessage $automaticMessage, Mailer $mailer, EventDispatcherInterface $dispatcher)
    {
        $user = $userRepository->findOneBy(['resetToken' => $token]);

        if (!$user){
            $this->addFlash('danger', 'le lien de confirmation a expiré');
            return $this->redirectToRoute('security_login');
        }

        /****send welcome email *****/
        $mailer->sendEmailWithTemplate($user->getEmail(), null, 4);
        /*****end ******/

        $user->setResetToken(null);
        $user->setIsValid(1);

        $manager->persist($user);

        if($user->getCompany() != null) {
            $company = $companyRepository->findOneBy(['id' => $user->getCompany()]);
            if ($company != null) {
                $company->setIsValid(true);
                $manager->persist($company);
            }
        }
        $manager->flush();


        /*****check if the user is coming from invitation****/
        $sponsor =  $sponsorshipRepository->findOneBy(['email'=> $user->getEmail()]) ;
        $pointsSender = $scorePointRepository->findOneBy(['id' => 5])->getPoint();
        $pointsReceiver = $scorePointRepository->findOneBy(['id' => 4])->getPoint();
        $optionsRedirect = [];
        if ($sponsor  != null){
            $scoreHandler->add($sponsor->getUser(), $pointsSender);
            $scoreHandler->add($user, $pointsReceiver);
            $optionsRedirect = ['toastScore' => $pointsReceiver];

            /* add chat message to sponsor */
            $automaticMessage->fromAdvisorToSponsored($sponsor, $user);
            $automaticMessage->fromAdvisorToSponsor($sponsor, $user);
        }

        $guardHandler->authenticateUserAndHandleSuccess(
            $user,
            $request,
            $authenticator,
            'main'
        );
        $this->addFlash('success', 'votre compte a été activé');

        //Dispatch on Logger Entity Event
        $dispatcher->dispatch(new LoggerEntityEvent(LoggerEntityEvent::USER_NEW, $user));

        return $this->redirectToRoute('dashboard', $optionsRedirect);
    }

    /**
     * @Route("/cgu/accept", name="cgu_accept")
     */
    public function cguAccept(Request $request, CookieAcceptedSession $cookieAcceptedSession)
    {
        $cookieAcceptedSession->addCookie($request);

        return $this->json(true);
    }
}
