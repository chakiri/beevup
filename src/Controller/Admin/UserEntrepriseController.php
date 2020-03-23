<?php
namespace App\Controller\Admin;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use App\Entity\User;
use App\Entity\Profile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserEntrepriseController extends EasyAdminController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
  
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    
    public function persistUserEntrepriseEntity($user)
    {
        $currentUser = $this->getUser();
        $user->setStore($currentUser->getStore());
        if($currentUser->getCompany()!= null )
        {
            $user->setCompany($currentUser->getCompany());
        }
        $user->setRoles(['ROLE_USER']);
        $this->updatePassword($user);
        parent::persistEntity($user);
        $profile = new Profile();
        $profile->setUser($user);
        parent::persistEntity($profile);
   }

    public function updateUserEntrepriseEntity($user)
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
        if (!empty($user->getPassword())) {
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
}