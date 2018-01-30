<?php
include_class('handyman', 'location', 'inc/model/');

abstract class xml_model implements JsonSerializable
{
    public function to_xml(SimpleXMLElement &$xml, string $name)
    {
        $data = $this->convert_into_array();
        $xml_data = $xml->addChild("$name");
        foreach ($data as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $value->to_xml($xml_data, "$key");
            } else {
                $xml_data->addChild("$key", htmlspecialchars("$value"));
            }
        }
    }

    public function jsonSerialize()
    {
        return (object)get_object_vars($this);
    }

    public function to_array()
    {
        return (array)get_object_vars($this);
    }
}

class handyman_address_xml_model extends xml_model
{
    protected $Address1;
    protected $StreetNo;
    protected $Address2;
    protected $PostalCode;
    protected $PostalArea;

    public function set_Address1($Address1)
    {
        $this->Address1 = $Address1;
    }

    public function get_Address1()
    {
        return $this->Address1;
    }

    public function set_StreetNo($StreetNo)
    {
        $this->StreetNo = $StreetNo;
    }

    public function get_StreetNo()
    {
        return $this->StreetNo;
    }

    public function set_Address2($Address2)
    {
        $this->Address2 = $Address2;
    }

    public function get_Address2()
    {
        return $this->Address2;
    }

    public function set_PostalCode($PostalCode)
    {
        $this->PostalCode = $PostalCode;
    }

    public function get_PostalCode()
    {
        return $this->PostalCode;
    }

    public function set_PostalArea($PostalArea)
    {
        $this->PostalArea = $PostalArea;
    }

    public function get_PostalArea()
    {
        return $this->PostalArea;
    }
}

class handyman_customer_xml_model extends xml_model
{
    // Expect the customer to exist in Handyman
    protected $CustomerNo;

    public function __construct(int $customer_id = 0)
    {
        $this->CustomerNo = $customer_id;
    }

    public function set_CustomerNo($CustomerNo)
    {
        $this->CustomerNo = $CustomerNo;
    }

    public function get_CustomerNo()
    {
        return $this->CustomerNo;
    }
}

class handyman_location_xml_model extends xml_model
{
    protected $InstallationID;
    // $InstallationOrigin 0 = default(Own) 1 = preinstalled
    protected $InstallationOrigin = 0;
    protected $Name;

    //Site 0=Equipment, 1=Site
    protected $Site = 1;

    // Address is a handyman_address_xml_model
    protected $Address;

    // $InstallationIDParent has to be in the Handyman DB, optional
    //    protected $InstallationIDParent = 0;
    // ID of the employee responsible for the installation, must exist in Handyman
    protected $ResponsibleNo = 0;
    // Status 0=New (default for equipment), 1=Installed (default for site), 2=Paused, 3=Historical
    protected $Status = 1;
    // handyman_customer_xml_model
    protected $Customer;

    public function set_InstallationID($InstallationID)
    {
        $this->InstallationID = $InstallationID;
    }

    public function get_InstallationID()
    {
        return $this->InstallationID;
    }

    public function set_InstallationOrigin($InstallationOrigin)
    {
        $this->InstallationOrigin = $InstallationOrigin;
    }

    public function get_InstallationOrigin()
    {
        return $this->InstallationOrigin;
    }

    public function set_Name($Name)
    {
        $this->Name = $Name;
    }

    public function get_Name()
    {
        return $this->Name;
    }

    public function set_Site($Site)
    {
        $this->Site = $Site;
    }

    public function get_Site()
    {
        return $this->Site;
    }

    public function set_Address($Address)
    {
        $this->Address = $Address;
    }

    public function get_Address(): handyman_address_xml_model
    {
        return $this->Address;
    }

//    public function set_InstallationIDParent($InstallationIDParent)
//    {
//        $this->InstallationIDParent = $InstallationIDParent;
//    }
//
//    public function get_InstallationIDParent()
//    {
//        return $this->InstallationIDParent;
//    }

    public function set_ResponsibleNo($ResponsibleNo)
    {
        $this->ResponsibleNo = $ResponsibleNo;
    }

    public function get_ResponsibleNo()
    {
        return $this->ResponsibleNo;
    }

    public function set_Status($Status)
    {
        $this->Status = $Status;
    }

    public function get_Status()
    {
        return $this->Status;
    }

    public function set_Customer($Customer)
    {
        $this->Customer = $Customer;
    }

    public function get_Customer(): handyman_customer_xml_model
    {
        return $this->Customer;
    }

    public function __construct(handyman_location $loc, int $customer_id = 0)
    {
        $this->Customer = new handyman_customer_xml_model($customer_id);
        $this->Address = new handyman_address_xml_model();

        $this->InstallationID = $loc->get_location_code();
//        $this->InstallationOrigin = 0; // 0 = default
        $this->Name = $loc->get_loc1_name();
//        $this->Site = 1; // 0=Equipment, 1=Site
        $this->InstallationIDParent = 0; // only usefull for buildings when reffering to the property
//        $this->ResponsibleNo; //ID of the employee responsible for the installation, must exist in Handyman
//        $this->Status = 1; //0=New (default for equipment), 1=Installed (default for site), 2=Paused, 3=Historical
        $this->Address->set_Address1($loc->get_adresse1());
        $this->Address->set_PostalArea($loc->get_poststed());
        $this->Address->set_PostalCode($loc->get_postnummer());
    }

    public static function array_to_XML($arr): SimpleXMLElement
    {
        $xml = new SimpleXMLElement('<?xml version="1.0"?><InstallationList></InstallationList>');
        foreach ($arr as &$value) {
            $hm_xml_loc_obj = new handyman_location_xml_model($value, 0);
            $hm_xml_loc_obj->to_XML($xml, 'Installation');
        }
        return $xml;
    }
}