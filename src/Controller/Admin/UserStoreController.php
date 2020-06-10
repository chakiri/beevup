<?php
namespace App\Controller\Admin;
use App\Repository\UserRepository;
use App\Service\TopicHandler;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use App\Entity\User;
use App\Repository\UserTypeRepository;
use App\Entity\Profile;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\UserType;
use App\Service\Email;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class UserStoreController extends EasyAdminController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    private $userRepo;
    private $topicHandler;
    private $email;
    private $token;
    private $userTypeRepo;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepo, UserTypeRepository $userTypeRepo,TopicHandler $topicHandler, Email $email, TokenGeneratorInterface $tokenGenerator)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepo = $userRepo;
        $this->topicHandler = $topicHandler;
        $this->email = $email;
        $this->token = $tokenGenerator->generateToken();
        $this->userTypeRepo = $userTypeRepo;
    }
    
    public function persistUserStoreEntity($user)
    {
        $currentUser = $this->getUser();
        $user->setStore($currentUser->getStore());

        if($currentUser->getCompany()!= null )
        {
            $user->setCompany($currentUser->getCompany());
        }
        if($user->getType()->getId() == 1)
        {
            $user->setRoles(['ROLE_USER', 'ROLE_ADMIN_STORE']);
        }
        if($user->getType()->getId() == 2)
        {
            $user->setRoles(['ROLE_USER']);
        }
        $userTypePatron = $this->userTypeRepo->findOneBy(['id'=> 4]);
        $storePatron =$this->userRepo->findOneBy(['type'=> $userTypePatron, 'store'=>$user->getStore()]);

        /* add admin topics to user */
        $this->topicHandler->addAdminTopicsToUser($user);
        /* add store topic to user */
        $this->topicHandler->initStoreTopic($user->getStore(), $user);
      
        $this->updatePassword($user);
        $user->setResetToken($this->token);
        $user->setIsValid(true);
        parent::persistEntity($user);

        $profile = new Profile();
        $profile->setUser($user);
        parent::persistEntity($profile);
        /*send email confirmation*/
        $url = $this->generateUrl('security_new_account', ['token' => $this->token], UrlGeneratorInterface::ABSOLUTE_URL);
        $this->email->send($this->token, $url, $user,$storePatron,'createNewAccount.html.twig','Beev\'Up par Bureau Vallée | Inscription');

    }

    public function updateUserStoreEntity($user)
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
    public function createUserStoreEntityFormBuilder(User $entity, $view)
    {
         
         $formBuilder = parent::createEntityFormBuilder($entity, $view);
        
         $formBuilder->add('type', EntityType::class, [
            'class' => UserType::class,
            'attr' => [
                'data-widget' => 'select2',
            ],
            'query_builder' => function (UserTypeRepository $er) {
                $currentUser = $this->getUser();
                 return $er->findByType($currentUser);
            },
        ]);
        return $formBuilder;
    }

    protected function createListQueryBuilder($entityClass, $sortDirection, $sortField = null, $dqlFilter = null)
    {
        $store = $this->getUser()->getStore();
        $dqlFilter = sprintf('entity.store = %s', $store->getId());
        $dqlFilter .= sprintf(' and entity.type in (1,2,3,4) ');
        $list = parent::createListQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);
        return $list;
    }
    /*protected function removeEntity($entity)
    {
       if ($this->getUser()->getId() == $entity->getId()) {
            $this->addFlash('error', 'vous ne pouvez pas supprimer votre account.');

            return $this->redirectToRoute('easyadmin', ['action' => 'list', 'entity' => $this->entity['name']]);
        }

    }*/
}