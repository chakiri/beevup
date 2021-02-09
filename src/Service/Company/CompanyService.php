<?php


namespace App\Service\Company;
use App\Repository\UserRepository;


class CompanyService
{
    private $users;

    /**
     * Company constructor.
     */
    public function __construct(UserRepository $users)
    {
       $this->users = $users;
    }

  public function updateUsersStore($company ){

   $users =  $this->users->findBy(['company'=>$company->getId()]);
   $store = $company->getStore();
   foreach ($users as $user){
       $user->setStore($store);
   }
  }
}