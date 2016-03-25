<?php

namespace Aliznet\WCSBundle\Reader\ORM;

use Doctrine\ORM\EntityRepository;
use Pim\Bundle\BaseConnectorBundle\Reader\Doctrine\Reader;

/**
 * Category Reader.
 *
 * @author    aliznet
 * @copyright 2016 ALIZNET (www.aliznet.fr)
 */
class CategoryReader extends Reader
{
    /**
     * @var EntityRepository
     */
    protected $categoryRepository;

    /**
     * @var string
     */
    protected $excludedCategories;

    /**
     * @var string
     */
    protected $language;

    /**
     * @param EntityRepository $categoryRepository
     */
    public function __construct(EntityRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * get excludedCategories.
     *
     * @return string excludedCategories
     */
    public function getExcludedCategories()
    {
        return $this->excludedCategories;
    }

    /**
     * Set excludedCategories.
     *
     * @param string $excludedCategories excludedCategories
     *
     * @return AbstractProcessor
     */
    public function setExcludedCategories($excludedCategories)
    {
        $this->excludedCategories = $excludedCategories;

        return $this;
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
     * @param string $language excludedCategories
     *
     * @return AbstractProcessor
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return query
     */
    public function getQuery()
    {
        if (!$this->query) {
            $qb = $this->categoryRepository->createQueryBuilder('c');
            if ($this->getExcludedCategories() != '') {
                $categories = explode(',', $this->getExcludedCategories());
                $i = 0;
                foreach ($categories as $cat) {
                    if ($i == 0) {
                        $qb->where(
                                $qb->expr()->orX(
                                        $qb->expr()->neq('c.code', ':code'.$i)
                                )
                        );
                        $qb->setParameter('code'.$i, $cat);
                    } else {
                        $qb->andWhere(
                                $qb->expr()->orX(
                                        $qb->expr()->neq('c.code', ':code'.$i)
                                )
                        );
                        $qb->setParameter('code'.$i, $cat);
                    }
                    ++$i;
                    $children = $this->getCategoryChildren($cat);
                    if ($children != null) {
                        foreach ($children as $child) {
                            $qb->andWhere(
                                    $qb->expr()->orX(
                                            $qb->expr()->neq('c.code', ':code'.$i)
                                    )
                            );
                            $qb->setParameter('code'.$i, $child['code']);
                            ++$i;
                        }
                    }
                }
            }
            $qb
                    ->innerJoin('c.translations', 'at', 'WITH', 'at.locale='.'\''.$this->getLanguage().'\'')
                    ->orderBy('c.root')
                    ->addOrderBy('c.left');

            $this->query = $qb->getQuery();
        }

        return $this->query;
    }

    /**
     * @param string $categoryCode
     *
     * @return query
     */
    protected function getCategoryChildren($categoryCode)
    {
        $categoryId = $this->getCategoryId($categoryCode);
        if ($categoryId == null) {
            return;
        }
        $qb = $this->categoryRepository->createQueryBuilder('c');
        $qb->select('c.code')
                ->where(
                        $qb->expr()->orX(
                                $qb->expr()->eq('c.parent', ':parent')
                        )
                )
                ->setParameter('parent', $categoryId['id'])
                ->orwhere(
                        $qb->expr()->orX(
                                $qb->expr()->eq('c.root', ':root')
                        )
                )
                ->setParameter('root', $categoryId['id']);

        return $qb->getQuery()->getResult();
    }

    /**
     * Get category ID by its code.
     *
     * @param string $categoryCode
     *
     * @return category
     */
    protected function getCategoryId($categoryCode)
    {
        $qb = $this->categoryRepository->createQueryBuilder('c');
        $qb->select('c.id')
                ->where(
                        $qb->expr()->orX(
                                $qb->expr()->eq('c.code', ':code')
                        )
                )
                ->setParameter('code', $categoryCode);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return array
     */
    public function getConfigurationFields()
    {
        return array(
            'excludedCategories' => array(
                'options' => array(
                    'required' => false,
                    'label'    => 'aliznet_wcs_export.export.excludedCategories.label',
                    'help'     => 'aliznet_wcs_export.export.excludedCategories.help',
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
