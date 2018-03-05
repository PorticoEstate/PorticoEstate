<?php
/**
 * Created by PhpStorm.
 * User: eskil.saatvedt
 * Date: 01.03.2018
 * Time: 15:54
 */

namespace AppBundle\XmlModels;


class HmOrderListXMLModel
{
    /**
     * @var \HMOrderXMLModel[]
     **/
    private $Order;

    /**
     * HmOrderListXMLModel constructor.
     */
    public function __construct()
    {
        $this->Order = new HmOrderXMLModel();
    }

    /**
     * @return \HMOrderXMLModel[]
     */
    public function getOrder(): array
    {
        return $this->Order;
    }

}