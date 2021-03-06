<?php

namespace App\Controller\Admin;

use App\Service\TopicHandler;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use App\Entity\User;
use App\Entity\Profile;
use App\Repository\UserRepository;
use App\Repository\UserTypeRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Service\Mail\Mailer;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class UserCompanyController extends EasyAdminController
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

  
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, UserTypeRepository $userTypeRepo, UserRepository $userRepo, TopicHandler $topicHandler, Mailer $mailer, TokenGeneratorInterface $tokenGenerator)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->userTypeRepo = $userTypeRepo;
        $this->userRepo = $userRepo;
        $this->topicHandler = $topicHandler;
        $this->mailer = $mailer;
        $this->token = $tokenGenerator->generateToken();
    }
    
    public function persistUserCompanyEntity($user)
    {
        $currentUser = $this->getUser();
        $type = $this->userTypeRepo->findOneBy(['id'=> 6]);
        $user->setStore($currentUser->getStore());
        if($currentUser->getCompany()!= null )
        {
            $user->setCompany($currentUser->getCompany());
        }
        $userTypePatron = $this->userTypeRepo->findOneBy(['id'=> 4]);
        $storePatron =$this->userRepo->findOneBy(['type'=> $userTypePatron, 'store'=>$user->getStore(), 'isValid'=>1]);



        $user->setRoles(['ROLE_USER']);
        $user->setType($type);
        $user->setIsValid(true);
        $this->updatePassword($user);
        $user->setResetToken($this->token);
        parent::persistEntity($user);

        $profile = new Profile();
        $profile->setUser($user);
        parent::persistEntity($profile);

        /* add admin topics to user */
        //$this->topicHandler->addAdminTopicsToUser($user);
        /* add general community to user */
        $this->topicHandler->initGeneralStoreTopic($user);
        /* add company topic to user */
        $this->topicHandler->initCompanyTopic($currentUser->getCompany(), $user);
        /* add category company topic to user */
        $this->topicHandler->initCategoryCompanyTopic($currentUser->getCompany()->getCategory(), $user);

        /*send email confirmation*/
        $url = $this->generateUrl('security_new_account', ['token' => $this->token], UrlGeneratorInterface::ABSOLUTE_URL);
        $params = ['url' => $url, 'userStore' => $user->getStore()->getName(), 'sender' => ['name' => $this->getUser()->getProfile()->getLastname() . ' ' . $this->getUser()->getProfile()->getFirstname(), 'store' => $this->getUser()->getStore()->getName(), 'company' => $this->getUser()->getCompany() ? $this->getUser()->getCompany()->getName() : null]];
        $this->mailer->sendEmailWithTemplate($user->getEmail(), $params, 'inscription_invitation');

    }

    public function updateUserCompanyEntity($user)
    {
        $currentUser = $this->getUser();
        $user->setStore($currentUser->getStore());
        if($currentUser->getCompany()!= null )
        {
            $user->setCompany($currentUser->getCompany());
        }


        $this->updatePassword($user);
        parent::updateEntity($user);
        
    }

    public function updatePassword(User $user)
    {   
        if (!empty($user->getPassword()) && strlen($user->getPassword())< 50) {
             $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
        }
    }
    protected function createListQueryBuilder($entityClass, $sortDirection, $sortField = null, $dqlFilter = null)
    {
        $company = $this->getUser()->getCompany();
        $user = parent::getUser();
        $user = $user->getRoles();
        $dqlFilter = sprintf('entity.company = %s', $company->getId());
        $list = parent::createListQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);
    
    
        return $list;
    }

    protected function createSearchQueryBuilder($entityClass, $searchQuery, array $searchableFields, $sortField = null, $sortDirection = null, $dqlFilter = null)
    {
        $company = $this->getUser()->getCompany();
        $qb = parent::createSearchQueryBuilder($entityClass, $searchQuery, $searchableFields, $sortField, $sortDirection, $dqlFilter);
        if ($entityClass === User::class) {
            $qb->innerJoin('entity.profile', 'P')
                ->orWhere('LOWER(P.firstname) LIKE :search or LOWER(P.lastname) LIKE :search')
                ->andWhere('entity.company = :company')
                ->setParameter('search','%'.$searchQuery.'%')
                ->setParameter('company',$company)
            ;
        }
        return $qb;
    }
}