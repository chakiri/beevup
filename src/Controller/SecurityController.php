<?php

namespace App\Controller;

use App\Event\Logger\LoggerEntityEvent;
use App\Form\AccountDtoType;
use App\Model\AccountDto;
use App\Repository\ScorePointRepository;
use App\Security\LoginFormAuthenticator;
use App\Service\Chat\AutomaticMessage;
use App\Service\Mail\ContactsHandler;
use App\Service\Session\CookieAcceptedSession;
use App\Service\ScoreHandler;
use App\Service\Mail\Mailer;
use App\Service\Sponsor\FromInvitation;
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
     * @Route("/inscription", name="security_registration")
     */
    public function inscription(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder, UserTypeRepository $userTypeRepository, BarCode $barCode, CompanyRepository $companyRepo, UserRepository $userRepository, TopicHandler $topicHandler, TokenGeneratorInterface $tokenGenerator, Mailer $mailer, ContactsHandler $contactsHandler): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Insert company data
            $company = new Company();
            $userType = $userTypeRepository->findOneBy(['id' => 3]);
            /*$userTypePatron = $userTypeRepository->findOneBy(['id' => 1]);
            $storePatron = $userRepository->findOneBy(['type' => $userTypePatron, 'store' => $user->getStore(), 'isValid'=>1]);*/

            $company->setSiret($form->get('company')->getData()->getSiret());
            $company->setName($form->get('name')->getData());
            $company->setEmail($user->getEmail());
            $company->setStore($user->getStore());
            $company->setCountry('FR');

            if ($form->get('addressNumber')->getData()) $company->setAddressNumber($form->get('addressNumber')->getData());
            if ($form->get('addressStreet')->getData()) $company->setAddressStreet($form->get('addressStreet')->getData());
            if ($form->get('addressPostCode')->getData()) $company->setAddressPostCode($form->get('addressPostCode')->getData());
            if ($form->get('city')->getData()) $company->setCity($form->get('city')->getData());

            //Generate bar code
            $companyId = $companyRepo->findOneBy([], ['id' => 'desc'])->getId() + 1;
            $company->setBarCode($barCode->generate($companyId));

            $manager->persist($company);

            //Insert user data
            $user->setStore($user->getStore());
            $user->setCompany($company);
            $user->setType($userType);
            $user->setPassword($passwordEncoder->encodePassword($user, $user->getPassword()));

            //Add admin topics to user
            $topicHandler->initGeneralStoreTopic($user);
            //Add company topic to user
            $topicHandler->initCompanyTopic($company, $user);

            //Generate Token
            $token = $tokenGenerator->generateToken();
            $user->setResetToken($token);

            $manager->persist($user);

            //New profile
            $profile = new Profile();
            $profile->setUser($user);
            $profile->setLastname($form->get('lastname')->getData());
            $profile->setFirstname($form->get('firstname')->getData());

            $manager->persist($profile);

            $manager->flush();

            //Send mail
            $mailer->sendEmailWithTemplate($user->getEmail(), ['url' => $this->generateUrl('security_confirm_email', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL)], 'confirm_inscription');

            //Create new contact on SendinBlue
            $contactsHandler->handleContactSendinBlueRegistartion($user);

            return $this->redirectToRoute('waiting_validation');
        }

        return $this->render('default/inscription.html.twig', [
            'RegistrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/confirmEmail/{token}", name="security_confirm_email")
     */
    public function confirmEmail(LoginFormAuthenticator $authenticator, Request $request, string $token, UserRepository $userRepository, EntityManagerInterface $manager, GuardAuthenticatorHandler $guardHandler, Mailer $mailer, EventDispatcherInterface $dispatcher, FromInvitation $fromInvitation, ContactsHandler $contactsHandler, SponsorshipRepository $sponsorshipRepository)
    {
        $user = $userRepository->findOneBy(['resetToken' => $token]);

        if (!$user){
            $this->addFlash('danger', 'le lien de confirmation a expiré');
            return $this->redirectToRoute('security_login');
        }

        //Send welcome email
        $mailer->sendEmailWithTemplate($user->getEmail(), null, 'welcome_message');

        $user->setResetToken(null);
        $user->setIsValid(true);

        $manager->persist($user);

        if($company = $user->getCompany()) {
            $company->setIsValid(true);
            $manager->persist($company);
        }

        $manager->flush();

        //check if the user is coming from invitation
        $sponsor =  $sponsorshipRepository->findOneBy(['email'=> $user->getEmail()]) ;
        $optionsRedirect = [];
        if ($sponsor){
            $pointsReceiver = $fromInvitation->handle($sponsor, $user);
            $optionsRedirect = ['toastScore' => $pointsReceiver];
        }

        //Authenticate User automaticaly
        $guardHandler->authenticateUserAndHandleSuccess($user, $request, $authenticator, 'main');

        //Change statut contact on SendinBlue
        $contactsHandler->handleContactSendinBlueValidateEmail($user);

        //Dispatch on Logger Entity Event
        $dispatcher->dispatch(new LoggerEntityEvent(LoggerEntityEvent::USER_NEW, $user));

        /*$this->addFlash('success', 'votre compte a été activé');*/

        //Set id profile in options
        $optionsRedirect['id'] = $user->getProfile()->getId();

        return $this->redirectToRoute('account_edit_all', $optionsRedirect);
    }

    /**
     * @Route("/waitingValidation", name="waiting_validation")
     */
    public function waitingValidation(): Response
    {
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
            $mailer->sendEmailWithTemplate($email, ['url' => $url], 'password_forgotten');


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
     * @Route("/cgu/accept", name="cgu_accept")
     */
    public function cguAccept(Request $request, CookieAcceptedSession $cookieAcceptedSession)
    {
        $cookieAcceptedSession->addCookie($request);

        return $this->json(true);
    }

    /**
     * @Route("/account/{id}/edit/all", name="account_edit_all")
     */
    public function accountAll(Profile $profile, Request $request, EntityManagerInterface $manager)
    {
        //Denied access
        if($profile->getUser() !== $this->getUser()) return $this->render('bundles/TwigBundle/Exception/error403.html.twig');

        //Get company from profile
        $company = $profile->getUser()->getCompany();

        //Hydrate our model from entities
        $dto = AccountDto::createFromEntity($profile, $company);

        //Create form using Dto
        $form = $this->createForm(AccountDtoType::class, $dto);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            //Populate entities from dto
            $profile->setGender($dto->gender);
            $profile->setLastname($dto->lastname);
            $profile->setFirstname($dto->firstname);
            $profile->setFunction($dto->function);
            $profile->setMobileNumber($dto->personalPhone);
            $profile->setIsCompleted(true);

            $company->setSiret($dto->siret);
            $company->setEmail($dto->email);
            $company->setName($dto->name);
            $company->setPhone($dto->companyPhone);
            $company->setWebsite($dto->website);
            $company->setCategory($dto->category);
            $company->setActivity($dto->activity);
            $company->setIsCompleted(true);

            $company->setAddressNumber($dto->addressNumber);
            $company->setAddressStreet($dto->addressStreet);
            $company->setAddressPostCode($dto->addressPostCode);
            $company->setCity($dto->city);
            $company->setCountry($dto->country);

            $manager->persist($profile);
            $manager->persist($company);

            $manager->flush();

            //Know which submit btn is clicked
            if ($form->get('saveAndAdd')->isClicked()){
                return $this->redirectToRoute('service_new', ['inscription' => true]);
            }
            return $this->redirectToRoute('dashboard', [
                'status' => 'inscription'
            ]);

        }

        return $this->render('security/account.html.twig', [
            'form' => $form->createView(),
            'company' => $company,
            'profile' => $profile,
        ]);
    }
}
