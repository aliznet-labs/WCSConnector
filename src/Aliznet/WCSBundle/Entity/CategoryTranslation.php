<?php

namespace Aliznet\WCSBundle\Entity;

use Pim\Bundle\CatalogBundle\Entity\CategoryTranslation as BaseCategoryTranslation;

/*
 * CategoryTranslation entity
 * @author    aliznet
 * @copyright 2016 ALIZNET (http://www.aliznet.fr/)
 * 
 */

class CategoryTranslation extends BaseCategoryTranslation
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $description;
    /**
     * @var string
     */
    protected $longDescription;
    /**
     * @var string
     */
    protected $keyword;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getLongDescription()
    {
        return $this->longDescription;
    }

    /**
     * @return string
     */
    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * @param type $name
     *
     * @return \Aliznet\WCSBundle\Entity\CategoryTranslation
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param type $description
     *
     * @return \Aliznet\WCSBundle\Entity\CategoryTranslation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param type $longDescription
     *
     * @return \Aliznet\WCSBundle\Entity\CategoryTranslation
     */
    public function setLongDescription($longDescription)
    {
        $this->longDescription = $longDescription;

        return $this;
    }

    /**
     * @param type $keyword
     *
     * @return \Aliznet\WCSBundle\Entity\CategoryTranslation
     */
    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;

        return $this;
    }
}
