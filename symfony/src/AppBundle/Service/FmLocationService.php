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
use AppBundle\Entity\GwApplication;
use AppBundle\Entity\CustAttribute;

class FmLocationService
{
    /* @var $em EntityManager */
    private  $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return \Doctrine\ORM\Persisters\Collection $customAttributes
     */
    private function getCustomAttributesForProperties(){
        $appForProperties = $this->em->getRepository(GwApplication::class)->findAppForProperties();
        $gwLocationId = $appForProperties->getLocations()->first()->getId();
        return $this->em->getRepository(CustAttribute::class)->findProperties($gwLocationId);
    }

    private function minifyObjectArrayOfProperties(array $customAttributes ){
        $result = array();
        /* @var $customAttribute CustAttribute */
        foreach($customAttributes as $customAttribute){
            $result = array_merge($result, $customAttribute->getMinfiedArray());
        }
        return $result;
    }

    public function addCustomFieldsForProperties($locations)
    {
        $customAttributes = $this->getCustomAttributesForProperties();
        $minifiedCA = $this->minifyObjectArrayOfProperties($customAttributes);
        /* @var $location FmLocation1 */
        foreach ($locations as $location){
            $location->setCustomAttributes($minifiedCA);
        }
    }

    /**
     * Return the value(s) for a given property, if the property is in both arrays it will look itself up in custom attributes and return its representation
     *
     * @param string $property
     * @param array $objectVars
     * @param array $customAttributes
     * @return mixed
     */
    public static function getValue(string $property, array $objectVars, array $customAttributes)
    {
        if (!array_key_exists($property, $objectVars)) {
            return null;
        }
        if (!array_key_exists($property, $customAttributes)) {
            return $objectVars[$property];
        }

        $index = $objectVars[$property];
        $type = $customAttributes[$property]['type'];
        if (!$index){
            return CustAttribute::getDefaultValue($type);
        }

        switch ($type){
            case 'LB':
                return $customAttributes[$property]['values'][$index];
                break;
            case 'R':
            case 'CH':
                if(!is_array($index)){
                    $index = array_filter(explode(',', $index));
                }
                return array_intersect_key($customAttributes[$property]['values'],array_flip($index));
                break;
            default:
                return CustAttribute::getDefaultValue($type);
        }
    }

}