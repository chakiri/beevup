<?php
namespace App\Controller\Admin;

use App\Repository\UserRepository;
use App\Repository\UserTypeRepository;
use App\Repository\StoreRepository;
use App\Service\TopicHandler;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use App\Entity\User;
use App\Entity\Profile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Service\Mailer;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class UserAdminController extends EasyAdminController
{
    /**
     * @var UserPasswordEncoderInterface
     */

    private $passwordEncoder;
    private $userTypeRepo;
    private $userRepo;
    private $topicHandler;
    private $mailer;
    private $token;
    private $storeRepo;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, UserTypeRepository $userTypeRepo, UserRepository $userRepo, StoreRepository $storeRepo, TopicHandler $topicHandler, Mailer $mailer, TokenGeneratorInterface $tokenGenerator)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->userTypeRepo = $userTypeRepo;
        $this->userRepo = $userRepo;
        $this->storeRepo = $storeRepo;
        $this->topicHandler = $topicHandler;
        $this->mailer = $mailer;
        $this->token = $tokenGenerator->generateToken();

    }

    public function persistUserEntity($user)
    {

        $this->updatePassword($user);
        $userRoles = $user->getRoles();

        $profile = new Profile();
        $profile->setUser($user);

        if(in_array('ROLE_ADMIN_STORE', $userRoles))
        {
            $type = $this->userTypeRepo->findOneBy(['id'=> 1]);
            $user->setType($type);
            $userStore = $this->storeRepo->findOneBy(['id'=>$user->getStore()->getId()]);
            if( $userStore->getDefaultAdviser() == null)
            {
                $userStore->setDefaultAdviser( $user);
            }

            /* add admin topics to user */
            //$this->topicHandler->addAdminTopicsToUser($user);
            /* add general community to user */
            $this->topicHandler->initGeneralStoreTopic($user);
            /* add store topic to user */
            $this->topicHandler->initStoreTopic($user->getStore(), $user);
            /* add admin store topic to user */
            $this->topicHandler->initAdminStoreTopic($user);
        }
        if(in_array('ROLE_ADMIN_COMPANY', $userRoles)){
            $type = $this->userTypeRepo->findOneBy(['id'=> 3]);
            $user->setType($type);
            $user->setIsValid(true);
            /* add admin topics to user */
            //$this->topicHandler->addAdminTopicsToUser($user);
            /* add general community to user */
            $this->topicHandler->initGeneralStoreTopic($user);
            if ($user->getCompany()){
                /* add company topic to user */
                $this->topicHandler->initCompanyTopic($user->getCompany(), $user);
                /* add category company topic to user */
                $this->topicHandler->initCategoryCompanyTopic($user->getCompany()->getCategory(), $user);
            }
        }
        if(in_array('ROLE_ADMIN_PLATEFORM', $userRoles)){
            $type = $this->userTypeRepo->findOneBy(['id'=> 5]);
            $user->setType($type);

        }
        if(in_array('ROLE_CONTRIBUTOR', $userRoles)){
            $type = $this->userTypeRepo->findOneBy(['id'=> 7]);
            $user->setType($type);

        }
        if(in_array('ROLE_USER', $userRoles)){
            if($user->getCompany()!= null)
            {
                $type = $this->userTypeRepo->findOneBy(['id'=> 6]);
            }
            else{
                $type = $this->userTypeRepo->findOneBy(['id'=> 2]);
            }

            $user->setType($type);

        }

        $user->setIsDeleted(0);
        $user->setRoles($userRoles);
        $this->updateRoles($user);
        $user->setResetToken($this->token);
        parent::persistEntity($user);
        parent::persistEntity($profile);

        /*send email confirmation*/
        $url = $this->generateUrl('security_new_account', ['token' => $this->token], UrlGeneratorInterface::ABSOLUTE_URL);
        //$content = ['url' => $url, 'user'=> $user, 'storePatron'=>null];
        //$this->mailer->sendEmail('Beev\'Up par Bureau Vallée | Inscription', $user->getEmail(), $content, 'createNewAccount.html.twig');
        $params = ['url' => $url, 'userStore' => $user->getStore(), 'sender' => ['name' => $this->getUser()->getProfile()->getLastname() . $this->getUser()->getProfile()->getFirstname(), 'store' => $this->getUser()->getStore(), 'company' => $this->getUser()->getCompany()]];
        $this->mailer->sendEmailWithTemplate($user->getEmail(), $params, 9);

    }

    public function updateUserEntity($user)
    {

        $userRoles = array_unique($user->getRoles());
        if(in_array('ROLE_ADMIN_STORE', $userRoles))
        {
            $type = $this->userTypeRepo->findOneBy(['id'=> 1]);
            $user->setType($type);
        }
        if(in_array('ROLE_ADMIN_COMPANY', $userRoles)) {
            $type = $this->userTypeRepo->findOneBy(['id' => 3]);
            $user->setType($type);
        }

        if(in_array('ROLE_ADMIN_PLATEFORM', $userRoles)){
            $type = $this->userTypeRepo->findOneBy(['id'=> 5], []);
            $user->setType($type);
        }

        $user->setRoles($userRoles);
        $this->updatePassword($user);
        parent::updateEntity($user);

    }

    public function updatePassword(User $user)
    {
        if (!empty($user->getPassword()) && strlen($user->getPassword())< 50) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
        }
    }
    public function updateRoles(User $user)
    {
        if (!empty($user->getRoles())) {
            $user->setRoles($user->getRoles());
        }
    }

    public function switchAction()
    {
        $id = $this->request->query->get('id');
        $user = $this->userRepo->findOneBy(['id' => $id]);

        return $this->redirectToRoute('dashboard', [
            '_switch_user' => $user->getUsername()
        ]);
    }
}