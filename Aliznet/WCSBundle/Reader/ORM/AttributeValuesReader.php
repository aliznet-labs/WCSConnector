<?php

namespace Aliznet\WCSBundle\Reader\ORM;

use Doctrine\ORM\EntityManager;
use Pim\Bundle\BaseConnectorBundle\Reader\Doctrine\Reader;

/**
 * Attribute Values Reader.
 *
 * @author    aliznet
 * @copyright 2016 ALIZNET (www.aliznet.fr)
 */
class AttributeValuesReader extends Reader
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $attributes;

    /**
     * @var string
     */
    protected $includeexclude;

    /**
     * @var string
     */
    protected $language;

    /**
     * @var int
     */
    protected $wcs;

    /**
     * @param EntityManager $em        The entity manager
     * @param string        $className The entity class name used
     */
    public function __construct(EntityManager $em, $className)
    {
        $this->em = $em;
        $this->className = $className;
    }

    /**
     * get attributes.
     *
     * @return string attributes
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set attributes.
     *
     * @param string $attributes attributes
     *
     * @return AbstractProcessor
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * get includeexclude.
     *
     * @return string includeexclude
     */
    public function getIncludeexclude()
    {
        return $this->includeexclude;
    }

    /**
     * Set includeexclude.
     *
     * @param string $includeexclude includeexclude
     *
     * @return AbstractProcessor
     */
    public function setIncludeexclude($includeexclude)
    {
        $this->includeexclude = $includeexclude;
    }

    /**
     * get language.
     *
     * @return string language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set language.
     *
     * @param string $language language
     *
     * @return AbstractProcessor
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get Query.
     *
     * @return Query
     */
    public function getQuery()
    {
        $this->query = $this->em
                ->getRepository($this->className)
                ->createQueryBuilder('av')
                ->innerJoin('av.attribute', 'at')
                ->orderBy('av.attribute')
                ->addOrderBy('av.sortOrder');

        $qb = $this->query;
        $this->QueryExludedWCSFields($qb);
        $this->QueryAttributes($qb);

        $this->query = $this->query->getQuery();

        return $this->query;
    }

    /**
     * Exclude WCS fileds from attributes export.
     *
     * @param query $qb
     *
     * @return query
     */
    public function QueryExludedWCSFields($qb)
    {
        $filename = 'exluded_atrributes.txt';
        $dir = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
        $file = $dir.'/web/WCS/'.$filename;
        $lines = file($file);
        if (file_exists($file) && $lines) {
            $fields = str_replace(array("\r\n", "\n", "\r"), '', $lines);
            $qb->where($qb->expr()->orX($qb->expr()->notIn('at.code', $fields)));
            $this->wcs = true;
        } else {
            $this->wcs = false;
        }

        return $qb;
    }

    /**
     * Include or exclude attributes in the configuration field from export.
     *
     * @param query $qb
     *
     * @return query
     */
    public function QueryAttributes($qb)
    {
        $include_exclude = $this->getIncludeexclude();
        $attributes = $this->getAttributes();
        $attributes_config = explode(',', $attributes);

        $condition = 'Where';
        if ($this->wcs) {
            $condition = 'andWhere';
        }
        switch ($include_exclude):
            case 'Exclude' :
                $qb->$condition($qb->expr()->orX($qb->expr()->notIn('at.code', $attributes_config)));
        break;
        case 'Include':
                $qb->$condition($qb->expr()->orX($qb->expr()->in('at.code', $attributes_config)));
        break;
        endswitch;

        return $qb;
    }

    /**
     * @return array
     */
    public function getConfigurationFields()
    {
        return array(
            'includeexclude' => array(
                'type'    => 'choice',
                'options' => array(
                    'choices'  => array('Exclude' => 'Exclude', 'Include' => 'Include'),
                    'required' => false,
                    'label'    => 'aliznet_wcs_export.export.includeexclude.label',
                    'help'     => 'aliznet_wcs_export.export.includeexclude.help',
                ),
            ),
            'attributes' => array(
                'options' => array(
                    'required' => false,
                    'label'    => 'aliznet_wcs_export.export.Attributes.label',
                    'help'     => 'aliznet_wcs_export.export.Attributes.help',
                ),
            ),
            'language' => array(
                'options' => array(
                    'required' => true,
                    'label'    => 'aliznet_wcs_export.export.language.label',
                    'help'     => 'aliznet_wcs_export.export.language.help',
                ),
            ),
        );
    }
}
