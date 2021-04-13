<?php

namespace App\Entity;

class Search
{
    private $name;

    private $service;

    private $company;

    private $isExclusif;

    private $category;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param mixed $service
     */
    public function setService($service): void
    {
        $this->service = $service;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $company
     */
    public function setCompany($company): void
    {
        $this->company = $company;
    }

    /**
     * @return mixed
     */
    public function getIsExclusif()
    {
        return $this->isExclusif;
    }

    /**
     * @param mixed $isExclusif
     */
    public function setIsExclusif($isExclusif): void
    {
        $this->isExclusif = $isExclusif;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category): void
    {
        $this->category = $category;
    }
}
