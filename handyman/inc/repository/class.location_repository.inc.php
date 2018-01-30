<?php
include_class('handyman', 'handyman_location', 'inc/model/');
include_class('phpgwapi', 'db_pdo', 'inc/');


abstract class handyman_repository
{

    const BOOL = 0;
    const INT = 1;
    const FLOAT = 2;
    const STR = 3;


    /**
     * Unmarchal database values according to type
     * @param $value the field value
     * @param $type    int Const declaration of type
     * @return the php value
     */

    protected function unmarshal($value, $type)
    {
        if ($value === null || $value == 'NULL') {
            if ($type === handyman_repository::STR) {
                return '';
            }
            return null;
        }
        switch ($type) {
            case handyman_repository::BOOL:
                return (bool)$value;
                break;
            case handyman_repository::INT:
                return (int)$value;
                break;
            case handyman_repository::FLOAT:
                return (float)$value;
                break;
            case handyman_repository::STR:
                return (string)$value;
            default:
                return (string)$value;
        }
    }
}

class handyman_location_repository extends handyman_repository
{
    protected $_db;

    function __construct(phpgwapi_db_pdo $db)
    {
        $this->_db = $db;
    }

    public function exists(string $location_code): bool
    {
        $query = "select exists(select 1 from public.fm_location1 where location_code = '$location_code')";
        $this->_db->query($query, __LINE__, __FILE__);
        $this->_db->next_record();
        return (bool)$this->_db->Record['exists'];
    }

    public function get_one_by_id(string $location_code)
    {
        if (!$this->exists($location_code)) {
            throw new Exception('Location do not exists');
        }

        $query = "SELECT Location.*, Category.descr AS category_descr, Owner.org_name as owner_org_name, Town.name as town_name FROM public.fm_location1 AS Location 
          LEFT JOIN public.fm_location1_category AS Category ON Location.category = Category.id 
          LEFT JOIN public.fm_part_of_town AS Town ON Location.part_of_town_id = Town.id
          LEFT JOIN public.fm_owner AS Owner ON Location.owner_id = Owner.id
          WHERE Location.location_code = '$location_code' AND Location.category != 99";
        $this->_db->query($query, __LINE__, __FILE__);
        $this->_db->next_record();
        return $this->unmarshal_all_fields();
    }

    protected function unmarshal_all_fields(): handyman_location
    {
        $loc = new handyman_location($this->unmarshal($this->_db->Record['id'], handyman_repository::INT));
        $loc->set_location_code($this->unmarshal($this->_db->Record['location_code'], handyman_repository::STR));
        $loc->set_loc1($this->unmarshal($this->_db->Record['loc1'], handyman_repository::STR));
        $loc->set_loc1_name($this->unmarshal($this->_db->Record['loc1_name'], handyman_repository::STR));
        $loc->set_part_of_town_id($this->unmarshal($this->_db->Record['part_of_town_id'], handyman_repository::INT));
        $loc->set_entry_date($this->unmarshal($this->_db->Record['entry_date'], handyman_repository::INT));
        $loc->set_category($this->unmarshal($this->_db->Record['category'], handyman_repository::INT));
        $loc->set_user_id($this->unmarshal($this->_db->Record['user_id'], handyman_repository::INT));
        $loc->set_owner_id($this->unmarshal($this->_db->Record['owner_id'], handyman_repository::INT));
        $loc->set_merknader($this->unmarshal($this->_db->Record['merknader'], handyman_repository::STR));
        $loc->set_change_type($this->unmarshal($this->_db->Record['change_type'], handyman_repository::INT));
        $loc->set_tips_objekt($this->unmarshal($this->_db->Record['tips_objekt'], handyman_repository::STR));
        $loc->set_merknader_2($this->unmarshal($this->_db->Record['merknader_2'], handyman_repository::STR));
        $loc->set_adresse1($this->unmarshal($this->_db->Record['adresse1'], handyman_repository::STR));
        $loc->set_adresse2($this->unmarshal($this->_db->Record['adresse2'], handyman_repository::STR));
        $loc->set_postnummer($this->unmarshal($this->_db->Record['postnummer'], handyman_repository::INT));
        $loc->set_poststed($this->unmarshal($this->_db->Record['poststed'], handyman_repository::STR));
        $loc->set_merknader_1($this->unmarshal($this->_db->Record['merknader_1'], handyman_repository::STR));
        $loc->set_aktiv($this->unmarshal($this->_db->Record['aktiv'], handyman_repository::INT));
        $loc->set_olje_tank($this->unmarshal($this->_db->Record['olje_tank'], handyman_repository::INT));
        $loc->set_gass_tank($this->unmarshal($this->_db->Record['gass_tank'], handyman_repository::INT));
        $loc->set_septik_tank($this->unmarshal($this->_db->Record['septik_tank'], handyman_repository::INT));
        $loc->set_brann_hydrant($this->unmarshal($this->_db->Record['brann_hydrant'], handyman_repository::INT));
        $loc->set_area_gross($this->unmarshal($this->_db->Record['area_gross'], handyman_repository::FLOAT));
        $loc->set_bronn($this->unmarshal($this->_db->Record['bronn'], handyman_repository::INT));
        $loc->set_fett_avskiller($this->unmarshal($this->_db->Record['fett_avskiller'], handyman_repository::INT));
        $loc->set_slam_avskiller($this->unmarshal($this->_db->Record['slam_avskiller'], handyman_repository::INT));
        $loc->set_mva($this->unmarshal($this->_db->Record['mva'], handyman_repository::INT));
        $loc->set_modified_by($this->unmarshal($this->_db->Record['modified_by'], handyman_repository::INT));
        $loc->set_modified_on($this->unmarshal($this->_db->Record['modified_on'], handyman_repository::INT));
        $loc->set_delivery_address($this->unmarshal($this->_db->Record['delivery_address'], handyman_repository::STR));

        // Items from other tables
        $loc->set_category_descr($this->unmarshal($this->_db->Record['category_descr'], handyman_repository::STR));
        $loc->set_town_name($this->unmarshal($this->_db->Record['town_name'], handyman_repository::STR));
        $loc->set_owner_org_name($this->unmarshal($this->_db->Record['owner_org_name'], handyman_repository::STR));
        return $loc;
    }

    public function get_object_list()
    {
        $arr = array();
        $query = 'SELECT Location.*, Category.descr AS category_descr, Owner.org_name AS owner_org_name, Town.name AS town_name FROM public.fm_location1 AS Location 
          LEFT JOIN public.fm_location1_category AS Category ON Location.category = Category.id 
          LEFT JOIN public.fm_part_of_town AS Town ON Location.part_of_town_id = Town.id
          LEFT JOIN public.fm_owner AS Owner ON Location.owner_id = Owner.id 
          WHERE Location.category != 99
          ORDER BY Location.location_code';

        $this->_db->query($query, __LINE__, __FILE__);
        while ($this->_db->next_record()) {
            $arr[] = $this->unmarshal_all_fields();
        }
        return $arr;
    }
}