<?php

namespace App\Entity;

class Search
{
    private $name;

    private $isService;

    private $isCompany;

    private $category;

    private $isDiscovery;

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
    public function getIsService()
    {
        return $this->isService;
    }

    /**
     * @param mixed $isService
     */
    public function setIsService($isService): void
    {
        $this->isService = $isService;
    }

    /**
     * @return mixed
     */
    public function getIsCompany()
    {
        return $this->isCompany;
    }

    /**
     * @param mixed $isCompany
     */
    public function setIsCompany($isCompany): void
    {
        $this->isCompany = $isCompany;
    }

    /**
     * @return mixed
     */
    public function getIsDiscovery()
    {
        return $this->isDiscovery;
    }

    /**
     * @param mixed $isDiscovery
     */
    public function setIsDiscovery($isDiscovery): void
    {
        $this->isDiscovery = $isDiscovery;
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
