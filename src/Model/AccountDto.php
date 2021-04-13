<?php

namespace App\Model;

use App\Entity\Company;
use App\Entity\Profile;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

class AccountDto
{
    /**
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     * @Assert\Email
     * @ORM\Column(type="string", length=255)
     */
    public $email;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="integer", length=255, nullable=true)
     */
    public $gender;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $lastname;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $firstname;

    /**
     * @Assert\NotBlank
     * @ORM\ManyToOne(targetEntity="App\Entity\UserFunction")
     */
    public $function;

    /**
     * @Assert\NotBlank
     * @Assert\Regex(pattern="/^[0-9]{14}+$/", message="Siret invalide")
     * @ORM\Column(type="string", length=191, unique=true)
     */
    public $siret;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    public $name;

    /**
     * @Assert\Length(max=255)
     * @ORM\Column(type="string", nullable=true)
     */
    public $addressNumber;

    /**
     * @Assert\Length(max=255)
     * @ORM\Column(type="string", nullable=true)
     */
    public $addressStreet;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $addressPostCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $country;

    /**
     * @Assert\Length(max=255)
     * @Assert\Regex("/\d{10}|\+33\d{9}|\+33\s\d{1}\s\d{2}\s\d{2}\s\d{2}\s\d{2}|\d{2}\s\d{2}\s\d{2}\s\d{2}\s\d{2}/")
     * @ORM\Column(type="string", nullable=true)
     */
    public $companyPhone;

    /**
     * @Assert\Length(max=255)
     * @Assert\Regex("/\d{10}|\+33\d{9}|\+33\s\d{1}\s\d{2}\s\d{2}\s\d{2}\s\d{2}|\d{2}\s\d{2}\s\d{2}\s\d{2}\s\d{2}/")
     * @ORM\Column(type="string", nullable=true)
     */
    public $personalPhone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex(
     * pattern="(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})",
     * match=true,
     * message="Veuillez saisir une URL valide"
     * )
     */
    public $website;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CompanyCategory")
     */
    public $category;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $otherCategory;

    /**
     * @ORM\Column(type="string", length=1500)
     */
    public $description;

    /**
     * Hydrate DTO by data entities
     *
     * @param Profile $profile
     * @param Company $company
     * @return static
     */
    public static function createFromEntity(Profile $profile, Company $company)
    {
        //new static : Instantiate object of this class
        $account = new static;

        $account->gender = $profile->getGender();
        $account->lastname = $profile->getLastname();
        $account->firstname = $profile->getFirstname();
        $account->function = $profile->getFunction();
        $account->personalPhone = $profile->getMobileNumber();

        $account->email = $company->getEmail();
        $account->siret = $company->getSiret();
        $account->name = $company->getName();
        $account->companyPhone = $company->getPhone();
        $account->website = $company->getWebsite();
        $account->category = $company->getCategory();
        $account->otherCategory = $company->getOtherCategory();
        $account->description = $company->getDescription();

        $account->addressNumber = $company->getAddressNumber();
        $account->addressStreet = $company->getAddressStreet();
        $account->addressPostCode = $company->getAddressPostCode();
        $account->city = $company->getCity();
        $account->country = $company->getCountry();

        return $account;
    }
}