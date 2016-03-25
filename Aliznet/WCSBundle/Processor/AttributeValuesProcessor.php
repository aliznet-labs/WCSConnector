<?php

namespace Aliznet\WCSBundle\Processor;

use Akeneo\Component\Batch\Item\AbstractConfigurableStepElement;
use Akeneo\Component\Batch\Item\ItemProcessorInterface;

/**
 * Attribute Values Processor.
 *
 * @author    aliznet
 * @copyright 2016 ALIZNET (www.aliznet.fr)
 */
class AttributeValuesProcessor extends AbstractConfigurableStepElement implements ItemProcessorInterface
{
    /**
     * @var string
     */
    protected $wcsattributetype;

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
     * @param Attribute $item
     *
     * @return array
     */
    public function process($item)
    {
        $result = array();
        $i = 0;
        foreach ($item->getOptionValues() as $value) {
            $result[$i]['Identifier'] = $item->getAttribute()->getCode();
            $result[$i]['ValueIdentifier'] = $item->getCode();
            $result[$i]['Sequence'] = 1;
            $result[$i]['value'] = $value->getValue();
            $result[$i]['LanguageId'] = $value->getLocale();
            $result[$i]['Delete'] = '';
            ++$i;
        }

        return $result;
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
}
