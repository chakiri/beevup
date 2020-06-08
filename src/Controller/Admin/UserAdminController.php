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
use App\Service\Email;
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
    private $email;
    private $token;
    private $storeRepo;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, UserTypeRepository $userTypeRepo, UserRepository $userRepo, StoreRepository $storeRepo, TopicHandler $topicHandler, Email $email,TokenGeneratorInterface $tokenGenerator)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->userTypeRepo = $userTypeRepo;
        $this->userRepo = $userRepo;
        $this->storeRepo = $storeRepo;
        $this->topicHandler = $topicHandler;
        $this->email = $email;
        $this->token = $tokenGenerator->generateToken();

    }

    public function persistUserEntity($user)
    {

        $this->updatePassword($user);
        $userRoles = $user->getRoles();

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
            $this->topicHandler->addAdminTopicsToUser($user);
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
            $this->topicHandler->addAdminTopicsToUser($user);
            /* add company topic to user */
            $this->topicHandler->initCompanyTopic($user->getCompany(), $user);
            /* add category company topic to user */
            $this->topicHandler->initCategoryCompanyTopic($user->getCompany()->getCategory(), $user);

        }
        if(in_array('ROLE_ADMIN_PLATEFORM', $userRoles)){
            $type = $this->userTypeRepo->findOneBy(['id'=> 5]);
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


        array_push($userRoles, 'ROLE_USER');



        $user->setIsDeleted(0);
        $user->setRoles($userRoles);
        $this->updateRoles($user);
        $user->setResetToken($this->token);
        parent::persistEntity($user);

        $profile = new Profile();
        $profile->setUser($user);

        parent::persistEntity($profile);
        /*send email confirmation*/
        $url = $this->generateUrl('security_new_account', ['token' => $this->token], UrlGeneratorInterface::ABSOLUTE_URL);
        $this->email->send($this->token, $url, $user,null,'createNewAccount.html.twig', 'Beev\'Up par Bureau Vallée - Confirmation de votre e-mail');

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


        array_push($userRoles, 'ROLE_USER');
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
}