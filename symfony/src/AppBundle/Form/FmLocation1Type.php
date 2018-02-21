<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FmLocation1Type extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('locationCode')->add('loc1Name')->add('partOfTownId')->add('entryDate')->add('category')->add('userId')->add('ownerId')->add('merknader')->add('changeType')->add('tipsObjekt')->add('merknader2')->add('adresse1')->add('adresse2')->add('postnummer')->add('poststed')->add('merknader1')->add('aktiv')->add('oljeTank')->add('gassTank')->add('septikTank')->add('brannHydrant')->add('areaGross')->add('bronn')->add('fettAvskiller')->add('slamAvskiller')->add('mva')->add('modifiedBy')->add('modifiedOn')->add('id')->add('deliveryAddress');
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\FmLocation1'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_fmlocation1';
    }


}
