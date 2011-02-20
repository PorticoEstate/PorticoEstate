<?php
phpgw::import_class('phpgwapi.yui');
phpgw::import_class('bim.bobimitem');
phpgw::import_class('bim.sobimitem');
/*
 * This class serves as the 'Controller' or 'Container' in a dependancy injection context
 */
interface uibimitem {
	public function showItems();
	public function showBimItem();
}
class bim_uibimitem implements uibimitem {
	private $db;

	public function __construct() {
		$this->bocommon = CreateObject('property.bocommon');

		$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
		$this->db = & $GLOBALS['phpgw']->db;
	}

	public $public_functions = array
	(
        'showItems' => true,
		'showBimItem' => true
	);
	
	public function showItems()
	{
		$GLOBALS['phpgw']->js->validate_file( 'yui3', 'yui/yui-min', 'phpgwapi' );
		$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'bim.modellist', 'bim' );
		$modelId = phpgw::get_var("modelId");
		//$modelId = 3;
		if(empty($modelId))
		{
			echo "No modelId!";
		}
		else
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('bim_showitems'));
			$sobimitem = new sobimitem_impl($this->db);
			$sobimitem->setModelId($modelId);
			$bobimitem = new bobimitem_impl();
			$bobimitem->setSobimitem($sobimitem);
			$items = $bobimitem->fetchItemsByModelId();
			$bimItems = array();
			$count = count(($items));
			foreach( $items as $bimItem)
			{
				/* @var $bimItem BimItem*/
				array_push($bimItems, $bimItem->transformObjectToArray());//$bimItem->);
			}
			//$bimItems = print_r($items, true);
			
			$data = array
			(
				'someData' => "data",
				'modelId' => $modelId,
				'count' => $count,
				'bimItems' => array("item" => $bimItems)
			);
			
			$this->setupBimCss();
			$GLOBALS['phpgw']->xslttpl->set_var('bimitems',$data);
		}
		
	}
	public function showBimItem()
	{
		/*$GLOBALS['phpgw_info']['flags']['noheader'] = false;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = false;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
			$GLOBALS['phpgw']->common->phpgw_header(true);*/
			
		$GLOBALS['phpgw']->js->validate_file( 'yui3', 'yui/yui-min', 'phpgwapi' );
		$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'bim.modellist', 'bim' );
		$modelGuid = phpgw::get_var("modelGuid");
		//$modelId = 3;
		if(empty($modelGuid))
		{
			echo "No guid!";
		}
		else
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('bim_showSingleItem'));
			$sobimitem = new sobimitem_impl($this->db);
			/* @var $bimItem BimItem */
			$bimItem = $sobimitem->getBimItem($modelGuid);			
    		$GLOBALS['phpgw']->xslttpl->set_xml_data($bimItem->getXml());
			$this->setupBimCss();
		}
	}
	
	private function testTemplate() {
		phpgw::import_class('phpgwapi.template_portico');
		$app = $GLOBALS['phpgw_info']['flags']['currentapp'];

		$GLOBALS['phpgw']->template->set_root("C:\\vBoxShare\\html\\dev-bim2\\bim\\templates\\portico");
		$GLOBALS['phpgw']->template->set_unknowns('remove');
		$GLOBALS['phpgw']->template->set_file('test', 'test.tpl');
		$tpl_vars = array
		(
			'test2'			=> "myTest"
		);
	
		$GLOBALS['phpgw']->template->set_var($tpl_vars);
		$GLOBALS['phpgw']->template->pfp('out', 'test');
		unset($tpl_vars);
	}
	private function setupBimCss() {
    if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) ) {
            $GLOBALS['phpgw']->css = createObject('phpgwapi.css');
        }
        $GLOBALS['phpgw']->css->add_external_file('bim/templates/base/css/bim.css');
    }
	

}
