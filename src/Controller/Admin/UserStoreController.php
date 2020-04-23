<?php
namespace App\Controller\Admin;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use App\Entity\User;
use App\Repository\UserTypeRepository;
use App\Entity\Profile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\UserType;
use App\Service\BarCode;

class UserStoreController extends EasyAdminController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    private $barCode;
    private $userRepo;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepo,  BarCode $barCode)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->barCode = $barCode;
        $this->userRepo = $userRepo;
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
        /*** generate bar code*/
        $userId =  $this->userRepo->findOneBy([],['id' => 'desc'])->getId() + 1;
        $user->setBarCode($this->barCode->generate( $userId));
        /**end ******/
      
        $this->updatePassword($user);
        parent::persistEntity($user);
        $profile = new Profile();
        $profile->setUser($user);
        parent::persistEntity($profile);
   }

    public function updateUserStoreEntity($user)
    {
        $currentUser = $this->getUser();
        $user->setStore($currentUser->getStore());
        if($currentUser->getCompany()!= null )
        {
            $user->setCompany($currentUser->getCompany());
        }


        $user->setBarCode($this->barCode->generate($user->getId()));
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
}