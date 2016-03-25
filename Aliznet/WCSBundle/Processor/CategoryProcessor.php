<?php

namespace Aliznet\WCSBundle\Processor;

use Pim\Bundle\BaseConnectorBundle\Processor\TransformerProcessor as BaseTransformerProcessor;

/**
 * Valid category creation (or update) processor.
 *
 * Allow to bind input data to a category and validate it
 *
 * @author    aliznet
 * @copyright 2016 ALIZNET (www.aliznet.fr)
 */
class CategoryProcessor extends BaseTransformerProcessor
{
    /**
     * @var string
     */
    protected $language;

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
     * Set exportedAttributes.
     *
     * @return string $language language
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return array
     */
    public function getConfigurationFields()
    {
        return array(
            'language' => array(
                'options' => array(
                    'required' => false,
                    'label'    => 'aliznet_wcs_export.export.language.label',
                    'help'     => 'aliznet_wcs_export.export.language.help',
                ),
            ),
        );
    }

    /**
     * @param Category $item
     *
     * @return array
     */
    public function process($item)
    {
        $result = array();
        $item->setLocale($this->getLanguage());
        $result['GroupIdentifier'] = $item->getCode();
        if ($item->getParentCode() === null) {
            $result['TopGroup'] = 'true';
        }
        $result['ParentGroupIdentifier'] = $item->getParentCode();
        $result['Sequence'] = '1';
        $result['Name'] = $item->getTranslation()->getName();
        $result['ShortDescription'] = $item->getTranslation()->getDescription();
        $result['LongDescription'] = $item->getTranslation()->getLongDescription();
        $result['Thumbnail'] = $item->getThumbnail();
        $result['FullImage'] = $item->getFullImage();
        $result['Keyword'] = $item->getTranslation()->getKeyword();
        $result['Delete'] = '0';

        $language = (String) $item->getTranslation()->getLocale();
        $variable = constant('Aliznet\WCSBundle\Resources\Constant\Constants::'.$language);

        $result['Language_id'] = $variable;

        return $result;
    }
}
