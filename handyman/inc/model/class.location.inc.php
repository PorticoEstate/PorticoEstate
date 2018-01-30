<?php
/**
 * Created by PhpStorm.
 * User: eskil.saatvedt
 * Date: 19.01.2018
 * Time: 08:48
 */

include_class('handyman', 'model', 'inc/model/');

class handyman_location extends handyman_model
{
    protected $id;
    protected $location_code;
    protected $loc1;
    protected $loc1_name;
    protected $part_of_town_id; // fm_part_of_town.id
    protected $entry_date;

    // fm_location1_category.id, Category 99 is filtered out
    protected $category;
    protected $user_id;
    protected $owner_id; // fm_owner.id
    protected $merknader;
    protected $change_type;
    protected $tips_objekt;
    protected $merknader_2;
    protected $adresse1;
    protected $adresse2;
    protected $postnummer;
    protected $poststed;
    protected $merknader_1;
    protected $aktiv;
    protected $olje_tank;
    protected $gass_tank;
    protected $septik_tank;
    protected $brann_hydrant;
    protected $area_gross;
    protected $bronn;
    protected $fett_avskiller;
    protected $slam_avskiller;
    protected $mva;
    protected $modified_by;
    protected $modified_on;
    protected $delivery_address;

    // fm_location1_category
    protected $category_descr;
    // fm_part_of_town
    protected $town_name;
    // fm_owner
    protected $owner_org_name;

    /**
     * Constructor.  Takes an optional ID.  If a contract is created from outside
     * the database the ID should be empty so the database can add one according to its logic.
     *
     * @param int $id the id of this composite
     */
    public function __construct(int $id = null)
    {
        parent::__construct($id);
        $this->id = (int)$id;
    }

    public function set_id($id)
    {
        $this->id = $id;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function set_location_code($location_code)
    {
        $this->location_code = $location_code;
    }

    public function get_location_code()
    {
        return $this->location_code;
    }

    public function set_loc1($loc1)
    {
        $this->loc1 = $loc1;
    }

    public function get_loc1()
    {
        return $this->loc1;
    }

    public function set_loc1_name($loc1_name)
    {
        $this->loc1_name = $loc1_name;
    }

    public function get_loc1_name()
    {
        return $this->loc1_name;
    }

    public function set_part_of_town_id($part_of_town_id)
    {
        $this->part_of_town_id = $part_of_town_id;
    }

    public function get_part_of_town_id()
    {
        return $this->part_of_town_id;
    }

    public function set_category($category)
    {
        $this->category = $category;
    }

    public function get_categoryn()
    {
        return $this->category;
    }

    public function set_entry_date($entry_date)
    {
        $this->entry_date = $entry_date;
    }

    public function get_entry_daten()
    {
        return $this->entry_date;
    }

    public function set_owner_id($owner_id)
    {
        $this->owner_id = $owner_id;
    }

    public function get_owner_id()
    {
        return $this->owner_id;
    }

    public function set_user_id($user_id)
    {
        $this->user_id = $user_id;
    }

    public function get_user_id()
    {
        return $this->user_id;
    }

    public function set_merknader($merknader)
    {
        $this->merknader = $merknader;
    }

    public function get_merknader()
    {
        return $this->merknader;
    }

    public function set_change_type($change_type)
    {
        $this->change_type = $change_type;
    }

    public function get_change_type()
    {
        return $this->change_type;
    }

    public function set_tips_objekt($tips_objekt)
    {
        $this->tips_objekt = $tips_objekt;
    }

    public function get_tips_objekt()
    {
        return $this->tips_objekt;
    }

    public function set_merknader_2($merknader_2)
    {
        $this->merknader_2 = $merknader_2;
    }

    public function get_merknader_2()
    {
        return $this->merknader_2;
    }

    public function set_adresse1($adresse1)
    {
        $this->adresse1 = $adresse1;
    }

    public function get_adresse1()
    {
        return $this->adresse1;
    }

    public function set_adresse2($adresse2)
    {
        $this->adresse2 = $adresse2;
    }

    public function get_adresse2()
    {
        return $this->adresse2;
    }

    public function set_postnummer($postnummer)
    {
        $this->postnummer = $postnummer;
    }

    public function get_postnummer()
    {
        return $this->postnummer;
    }

    public function set_poststed($poststed)
    {
        $this->poststed = $poststed;
    }

    public function get_poststed()
    {
        return $this->poststed;
    }

    public function set_merknader_1($merknader_1)
    {
        $this->merknader_1 = $merknader_1;
    }

    public function get_merknader_1()
    {
        return $this->merknader_1;
    }

    public function set_aktiv($aktiv)
    {
        $this->aktiv = $aktiv;
    }

    public function get_aktiv()
    {
        return $this->aktiv;
    }

    public function set_olje_tank($olje_tank)
    {
        $this->olje_tank = $olje_tank;
    }

    public function get_olje_tank()
    {
        return $this->olje_tank;
    }

    public function set_gass_tank($gass_tank)
    {
        $this->gass_tank = $gass_tank;
    }

    public function get_gass_tank()
    {
        return $this->gass_tank;
    }

    public function set_septik_tank($septik_tank)
    {
        $this->septik_tank = $septik_tank;
    }

    public function get_septik_tank()
    {
        return $this->septik_tank;
    }

    public function set_brann_hydrant($brann_hydrant)
    {
        $this->brann_hydrant = $brann_hydrant;
    }

    public function get_brann_hydrant()
    {
        return $this->brann_hydrant;
    }

    public function set_area_gross($area_gross)
    {
        $this->area_gross = $area_gross;
    }

    public function get_area_gross()
    {
        return $this->area_gross;
    }

    public function set_bronn($bronn)
    {
        $this->bronn = $bronn;
    }

    public function get_bronn()
    {
        return $this->bronn;
    }

    public function set_fett_avskiller($fett_avskiller)
    {
        $this->fett_avskiller = $fett_avskiller;
    }

    public function get_fett_avskiller()
    {
        return $this->fett_avskiller;
    }

    public function set_slam_avskiller($slam_avskiller)
    {
        $this->slam_avskiller = $slam_avskiller;
    }

    public function get_slam_avskiller()
    {
        return $this->slam_avskiller;
    }

    public function set_mva($mva)
    {
        $this->mva = $mva;
    }

    public function get_mva()
    {
        return $this->mva;
    }

    public function set_modified_by($modified_by)
    {
        $this->modified_by = $modified_by;
    }

    public function get_modified_by()
    {
        return $this->modified_by;
    }

    public function set_modified_on($modified_on)
    {
        $this->modified_on = $modified_on;
    }

    public function get_modified_on()
    {
        return $this->modified_on;
    }

    public function set_delivery_address($delivery_address)
    {
        $this->delivery_address = $delivery_address;
    }

    public function get_delivery_address()
    {
        return $this->delivery_address;
    }

    // Value from other tables
    public function set_category_descr($category_descr)
    {
        $this->category_descr = $category_descr;
    }

    // Value from other tables
    public function get_category_descr()
    {
        return $this->category_descr;
    }

    // Value from other tables
    public function set_town_name($town_name)
    {
        $this->town_name = $town_name;
    }

    // Value from other tables
    public function get_town_name()
    {
        return $this->town_name;
    }

    // Value from other tables
    public function set_owner_org_name($owner_org_name)
    {
        $this->owner_org_name = $owner_org_name;
    }

    // Value from other tables
    public function get_owner_org_name()
    {
        return $this->owner_org_name;
    }

    public static function array_to_XML($arr): SimpleXMLElement
    {
        $xml = new SimpleXMLElement('<?xml version="1.0"?><InstallationList></InstallationList>');
        foreach ($arr as &$value) {
            $value->to_XML($xml, 'Installation');
        }
        return $xml;
    }
}