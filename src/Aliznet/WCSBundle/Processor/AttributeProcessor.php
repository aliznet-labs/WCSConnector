<?php

namespace Aliznet\WCSBundle\Processor;

use Akeneo\Component\Batch\Item\AbstractConfigurableStepElement;
use Akeneo\Component\Batch\Item\ItemProcessorInterface;

/**
 * Attribute Processor.
 *
 * @author    aliznet
 * @copyright 2016 ALIZNET (www.aliznet.fr)
 */
class AttributeProcessor extends AbstractConfigurableStepElement
implements ItemProcessorInterface
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
     * @param type $item
     *
     * @return array
     */
    public function process($item)
    {
        $result = [];
        $result['Identifier'] = $item->getCode();
        $result['type'] = $this->processattributeType($item->getAttributeType());
        $result['label_'.$this->getLanguage() ] = $item->setLocale($this->getLanguage())->getLabel();
        $result['Sequence'] = 1;
        $result['Displayable'] = 'True';
        $result['Searchable'] = ($item->isUseableAsGridFilter()) ? 'True' : 'False';
        $result['Comparable'] = 'True';
        $result['Delete'] = '';

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

    /**
     * Get exportedAttributes.
     * 
     * @param string $attributetype attributetype
     * 
     * @return string $attributetype attributetype
     */
    public function processattributeType($attributetype)
    {
        $pim_attribute_type_integer = array('pim_catalog_boolean', 'pim_catalog_number');
        $pim_attribute_type_float = array('pim_catalog_mertic');
        $pim_attribute_type_double = array('pim_catalog_price_collection');
        $pim_attribute_type_string = array('pim_catalog_date', 'pim_catalog_file', 'pim_catalog_identifier', 'pim_catalog_image', 'pim_catalog_multiselect',
                                          'pim_catalog_simpleselect', 'pim_catalog_text', 'pim_catalog_textarea', );
        if (in_array($attributetype, $pim_attribute_type_integer)) {
            $this->wcsattributetype = 'integer';
        } elseif (in_array($attributetype, $pim_attribute_type_float)) {
            $this->wcsattributetype = 'float';
        } elseif (in_array($attributetype, $pim_attribute_type_double)) {
            $this->wcsattributetype = 'double';
        } elseif (in_array($attributetype, $pim_attribute_type_string)) {
            $this->wcsattributetype = 'string';
        } else {
            $this->wcsattributetype = $attributetype;
        }

        return $this->wcsattributetype;
    }
}
