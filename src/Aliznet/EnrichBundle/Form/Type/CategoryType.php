<?php

namespace Aliznet\EnrichBundle\Form\Type;

use Pim\Bundle\EnrichBundle\Form\Type\CategoryType as BaseCategoryType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Type for category properties.
 * 
 * @author    aliznet
 * @copyright 2016 ALIZNET (www.aliznet.fr)
 */
class CategoryType extends BaseCategoryType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('display', 'switch', [
            'required' => false,
                ]
        );
        $builder->add('thumbnail', 'text', [
            'required' => false,
                ]
        );
        $builder->add('fullImage', 'text', [
            'required' => false,
                ]
        );
        /*
         * New translatable fields added to the category view
         */
        $this->addTranslationField($builder, 'name');
        $this->addTranslationField($builder, 'description');
        $this->addTranslationField($builder, 'longDescription');
        $this->addTranslationField($builder, 'keyword');

        parent::buildForm($builder, $options);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param type                 $field
     */
    protected function addTranslationField(FormBuilderInterface $builder, $field)
    {
        $builder->add(
            $field,
            'pim_translatable_field',
            [
                'field'             => $field,
                'translation_class' => $this->translationdataClass,
                'entity_class'      => $this->dataClass,
                'property_path'     => 'translations',
            ]
        );
    }
}
