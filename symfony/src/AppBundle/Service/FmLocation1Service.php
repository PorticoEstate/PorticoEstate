<?php
/**
 * Created by PhpStorm.
 * User: eskil.saatvedt
 * Date: 26.02.2018
 * Time: 15:07
 */

namespace AppBundle\Service;

use AppBundle\Entity\FmLocation1 as FmLocation1;
use Doctrine\ORM\ArrayCollection as ArrayCollection;
use Doctrine\ORM\EntityManager as EntityManager;
use AppBundle\Entity\Application;
use AppBundle\Entity\CustAttribute;

class FmLocation1Service
{
    /* @var $em EntityManager */
    private  $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getCustomAttributesForProperties(){
        $appForProperties = $this->em->getRepository(Application::class)->findAppForProperties();
        $gwLocationId = $appForProperties->getLocations()->first()->getId();
        return $this->em->getRepository(CustAttribute::class)->findProperties($gwLocationId);
    }

    public function addCustomFieldsForProperties($locations)
    {
        $customAttributes = $this->getCustomAttributesForProperties();
        /* @var $location FmLocation1 */
        foreach ($locations as $location){
            $location->setCustomAttributes($customAttributes);
        }
    }

    public static function getValue(string $property, array $objectVars, array $customAttributes)
    {
        if (!array_key_exists($property, $objectVars)) {
            return '';
        }

        $item = null;
        /* @var $custAttribute CustAttribute */
        foreach ($customAttributes as $custAttribute) {
            if ($property == $custAttribute->getColumnName()) {
                $item = $custAttribute;
                break;
            }
        }

        // If we have the attribute but it is not a custom attribute
        if (!$item) {
            return $objectVars[$property];
        }

        // if this is a List box (single select)
        if ($item->getDataType() == 'LB') {
            $index = $objectVars[$property];
            if (!is_int($index))
            {
                return '';
            }

            $choices = $item->getCustChoices();
            /* @var $choice CustChoice */
            foreach ($choices as $choice) {
                if ($index == $choice->getId()) {
                    return $choice->getValue();
                }
            }
        }
        return '';
    }

}