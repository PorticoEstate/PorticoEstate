<?php
	/**
	* EMail - Addressbook Chooser
	*
	* @author Written by Alex Borges <alex@sogrp.com>
	* @author Dave Hall <dave.hall@mbox.com.au>
	* @author Gerardo Ramirez <gramirez@grupogonher.com>
	* @copyright Copyright (C) 2001-2002 Angelo Tony Puglisi (Angles)
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @version $Id$
	*/


	/**
	* Addressbook Chooser
	*
	* This class has practically no state except for the categories cache. 
	* What im trying to say here is that this class does not query, does not 
	* call the contacts backend at all  it only visually represents whatever 
	* the bo class has in its state.
	*
	* Look at the templates. There is a frameset template that decares two frames.
	* The mainframe, and the secondary frame. 
	*
	* The frameset:
	* The frameset template is the main startpoint for this whole system. This
	* template declares the main and secondary frames. More importantly, this frame
	* declares all functions that are events in the framework. Look into the comments
	* in the frameset template to understand this event framework.
	*
	* The mainframe:
	* The mainframe is not a large computer. It is an html frame that actaully 
	* presents all the user interface to the browser. In the frameset template
	* you can see it declared as holding 100% of the screen. And is the frame
	* where all the buttons go. Its template file is addressbook-js.tpl
	* It should be named addressbook-js-mainframe.tpl in the next release
	*
	* The secondaryframe:
	* This frame does not have a corresponding template. It serves only as a
	* medium of communication with this class by the js framework. This frame
	* is directed to the diferent public functions in this class designed to
	* generate javascript arrays, strings and onLoad="funcall" type function calls
	* to the mainframe. The whole idea is that we server back data in js and tell
	* the main frame its data is here by sending a call by the onLoad event (which 
	* is called just after we finish passing our data.
	* @package email
	*/	
	class uijsaddressbook
	{
		var $public_functions=array ('show' => True,
					     'show_mainframe' => True,
					     'show_userdata' => True,
					     'set_destboxes' => True,
					     'forget_destboxes' => True,
					     'forget_all' => True);
		//@param viewmore The value of the viewmore checkbox
		var $viewmore;
		//@param fontsizes The font sizes array that relates screensize to font size in the 
		//renderd css for the main frame
		var $fontsizes=array(
				'700' =>'xx-small', //800x600 screens
				'800'  => 'x-small', //1024x768 screens
				'900'  => 'small' //1600x1200 screens
				);
		//@param searchbox The value of the top search box. This will be the value of the query
		var $searchbox;
		//@param template  We use our own template, cant use the global one if we want to be called
		//from another app...which is one of the objectives of the present class
		var $template;
		//@param toselectbox this exactly what is in the toselectbox input selectbox on the mainform form
		var $toselectbox;
		//@param ccselectbox ditto, ccselectbox
		var $ccselectbox;
		//@param bccselectbox ditto, bccselectbox
		var $bccselectbox;
		//@param cat_id Whatever category id was selected in the category selectbox
		var $cat_id;
		//@param catlist This holds a string of <option> which is given by the getlist call to the category class
		//We cache this result the first time we are called
		var $catlist;
		//@param categoryobject the phpgwapi.category object
		var $categoryobject;
		//@param boxcoming deprecated
		var $boxcoming=false;
		//@param start The value of the start parameter to be passed to the bo class for the query
		var $start;
		//@param order The value of the orther parameter to be passed to the bo class for the query
		var $order;
		//@param filter The value of the filter parameter to be passed to the bo class for the query
		//this value is set by selecting in the drop down right next to the searchautocomplete 
		//item
		var $filter;
		//@param sort The value of the filter parameter to be passed to the bo class for the query
		var $sort;
		//@param nameselectbox The value of the selected fields on the name selectbox (center select box)
		var $nameselectbox;
		//@param searchautocomplete The value of the searchautocomplete textbox, just above the name selectbox
		var $searchautocomplete;
		//@param hideto If this is set to 1, this framework will hide the toselctbox
		var $hideto;
		//@param hidecc If this is set to 1, this framework will hide the ccselctbox
		var $hidecc;
		//@param hidebcc If this is set to 1, this framework will hide the bccselctbox
		var $hidebcc;
		//@param first_column_label What to show as text at the top of the first column
		var $first_column_label="Select Categories";
		//@param second_column_label What to show as text at the top of the second column
		var $second_column_label="Select Destination";
		//@param third_column_label What to show as text at the top of the third column
		var $third_column_label="Destination Options";
		//@param search_button_value The value to show as "label" for the search button
		var $search_button_value="Search";
		//@param to_button_value The value to show as "label" for the to button
		var $to_button_value="To";
		//@param cc_button_value The value to show as "label" for the cc button
		var $cc_button_value="CC";
		//@param bcc_button_value The value to show as "label" for the cc button
		var $bcc_button_value="BCC";
		//@param cancel_button_value The value to show as "label" for the cancel button
		var $cancel_button_value="Cancel";
		//@param done_button_value The value to show as "label" for the done button
		var $done_button_value="Done";
		//@param remove_button_value The value to show as "label" for the remove button
		var $remove_button_value="Remove";
		//@param bcc_button_value The value to show as "label" for the bcc button
		var $none_button_value="None";
		//@param selected_category_name The selected category to show at the top of the second column 
		var $selected_category_name;
		//@param select_query_command the string to show in the query command drop down box as default
		var $select_query_command="Select Command";
		//@param go_query_command the string to show in the query command as the Search command
		var $go_query_command="Search";
		//@param clear_query_command the string to show in the query command as the clear query command
		var $clear_query_command="Clear Query";
		//@param selected_cat_name of the currently selected category
		var $selected_cat_name;
		
		
		//@param globaluid Okay, this is a very strange feature but i need it for my own deployment
		//if this value is set to something, an extra filter option will appear. It will
		//say global directory. My personal way to doing the global directory trick
		//was to make a user called addressmaster, who exports his ontacts to all
		//this valueshould be the addressmaster's uid for your deployment
		var $globaladduid='';
		//@param field_trans
		//This array converts field names of the contact backend (or the bo class for that matter)
		//to english so they can be passed through a lang called to be shown to the user
		var $field_trans = array(
				'fn'                  => 'full name',
				'sound'               => 'Sound',
				'org_name'            => 'company name',
				'org_unit'            => 'department',
				'title'               => 'title',
				'n_prefix'            => 'prefix',
				'n_given'             => 'first name',
				'n_middle'            => 'middle name',
				'n_family'            => 'last name',
				'n_suffix'            => 'suffix',
				'label'               => 'label',
				'adr_one_street'      => 'business street',
				'adr_one_locality'    => 'business city',
				'adr_one_region'      => 'business state',
				'adr_one_postalcode'  => 'business zip code',
				'adr_one_countryname' => 'business country',
				'adr_one_type'        => 'business address type',
				'adr_two_street'      => 'home street',
				'adr_two_locality'    => 'home city',
				'adr_two_region'      => 'home state',
				'adr_two_postalcode'  => 'home zip code',
				'adr_two_countryname' => 'home country',
				'adr_two_type'        => 'home address type',
				'tz'                  => 'time zone',
				'geo'                 => 'geo',
				'tel_work'            => 'business phone',
				'tel_home'            => 'home phone',
				'tel_voice'           => 'voice phone',
				'tel_msg'             => 'message phone',
				'tel_fax'             => 'fax',
				'tel_pager'           => 'pager',
				'tel_cell'            => 'mobile phone',
				'tel_bbs'             => 'bbs phone',
				'tel_modem'           => 'modem phone',
				'tel_isdn'            => 'isdn phone',
				'tel_car'             => 'car phone',
				'tel_video'           => 'video phone',
				'tel_prefer'          => 'preferred phone',
				'email'               => 'business email',
				'email_type'          => 'business email type',
				'email_home'          => 'home email',
				'email_home_type'     => 'home email type',
				'address2'            => 'address line 2',
				'address3'            => 'address line 3',
				'ophone'              => 'Other Phone',
				'bday'                => 'birthday',
				'url'                 => 'url',
				'pubkey'              => 'public key',
				'note'                => 'notes'
					);

		//@function uijsaddressbook constructor
		//@discussion
		//This is the main initializator. Here, we output the mainframe. Tats all we do. Its an important function
		//because the links for the events of the secondaryframe are built and presented to the browser
		//so that when the mainframe triggers an event, it will call some functions in the frameset (its window.parent)
		//And when the secondary frame returns from this public functions (which are all declared and defined in this file)
		//It will also call some of the functions in the frameset.
		function __construct()
		{
			//We create our template
						
			$this->template=CreateObject('phpgwapi.Template');
			//We set its root (we need to be called from other apps as well)
			//$this->template->set_root(PHPGW_SERVER_ROOT."/email/templates/".$GLOBALS['phpgw_info']['user']['preferences']['common']['template_set']);
			// (angles) fix suggested by Dave Hall allows email app compose page to be used with any template, 
			// fixes bug where email app compose page needed an (empty) template named dir in the email dir tree to use said named template 
			//$this->template->set_root($GLOBALS['phpgw']->common->get_tpl_dir('email'));
			$this->template->set_root(PHPGW_APP_TPL);
			$this->template->set_file(array(
						'mainframe_t' => 'addressbook-js-frameset.tpl'
						));
			//Set the charset (important for translations)
			$charset = $GLOBALS['phpgw']->translation->translate('charset');
			//Set your normal variables
			$this->template->set_var('charset',$charset);
			$this->template->set_var('title',$GLOBALS['phpgw_info']['site_title']);
			$this->template->set_var('bg_color',$GLOBALS['phpgw_info']['theme']['bg_color']);
			//Dont forget to include oour main javascript file
			$this->template->set_var('include_link',$GLOBALS['phpgw']->link("/email/inc/selectboxes.js"));
			$this->template->set_var('font',$GLOBALS['phpgw_info']['theme']['font']);
			//Get the hider values where needed
			$this->hideto  = $_POST['hideto'] ? $_POST['hideto'] : $_GET['hideto'];
			$this->hidecc  = $_POST['hidecc'] ? $_POST['hidecc'] : $_GET['hidecc'];
			$this->hidebcc  = $_POST['hidebcc'] ? $_POST['hidebcc'] : $_GET['hidebcc'];
			$this->cat_id  = $_POST['cat_id'] ? $_POST['cat_id'] : $_GET['cat_id'];
			//Set the link to the main frame...see, this is what im talking about
			//asd soon as this frameset is server to the browser it will go GET
			//the given url which is a call to the public function show_mainframe
			//declared and defined bellow....see what i mean?
			$this->template->set_var('mainframe_link', $GLOBALS['phpgw']->link(
													"/index.php",
													array(
													"menuaction"=>"email.uijsaddressbook.show_mainframe",
													"viewmore" => "1",
													"cat_id" => $this->cat_id,
													"hideto" => $this->hideto,
													"hidecc" => $this->hidecc,
													"hidebcc" => $this->hidebcc
						     								)
													)
							    );
			//Set the link for the showuserdata js event. The mainframe will call the get_user_data function
			//on the frameset if it finds that u have the viewmore checkbox turned on and u clikc on a single
			//name in the nameselectbox or any of the destination boxes. The get_user_data function
			//simply directs the secondary frame to this url which acutally passes back the
			//record for the needed user in urlencoded form as a js string.
			//This show_userdata public function will also aoutput the body onLoad event call and
			//will set it to window.parent.userdata_got, so thats the function that will get called in the parent.
			//That function, in turn, calls the event_userdata_got in the mainframe which actually updates
			//the contents of the userdata textarea as well as its size. Cool huh?
			$this->template->set_var('get_user_data_link', $GLOBALS['phpgw']->link(
													"/index.php",
													array(
													"menuaction"=>"email.uijsaddressbook.show_userdata",
													"viewmore" => "1",
													"cat_id" => "-1"
						     								)
													
													)
							    );
			//This does the same as the above (mainly) but it directs the secondary frame to
			//the set_destboxes function. This is a very complex function that, abstractly, sets
			//whatever values we pass it through GET, to the destboxes on the bo class (by a call to
			//bo->set_destboxes) and outputs the emails of those users being set.
			//It does this in three javascripts array per destination box....lets not get into
			//that (look for documentation in selectboxes.js), but it works okay.
			$this->template->set_var('set_destboxes_link', $GLOBALS['phpgw']->link(
													"/index.php",
													array(
													"menuaction"=>"email.uijsaddressbook.set_destboxes",
													"hideto" => $this->hideto,
													"hidecc" => $this->hidecc,
													"hidebcc" => $this->hidebcc,
													"viewmore" => "1",
													"cat_id" => "-1"
						     								)
													
													)
							    );
			//This is another event that makes this class call bo->forget_all and makes us forget about our own data too
			//which are only catlist
			$this->template->set_var('forget_all_link', $GLOBALS['phpgw']->link(
													"/index.php",
													array(
													"menuaction"=>"email.uijsaddressbook.forget_all"
						     								)
													
													)
							    );
			//Important to know our own screensize...
			$this->scrsize=$GLOBALS['phpgw_info']['user']['preferences']['email']['js_addressbook_screensize'];
			
		}
		//@function show
		//@abstract public function merely outputs the parsed frameset template
		function show()
		{
			$this->template->parse('out','mainframe_t',True);
			$this->template->p('out');
			//$GLOBALS['phpgw']->common->phpgw_exit();
			// (angles) param False tells it now to try parse_navber_end in phpgwapi functions.inc.php, eliminates this php error:
			// PHP Fatal error:  Call to undefined function:  parse_navbar_end() in /phpgwapi/inc/footer.inc.php on line 62
			$GLOBALS['phpgw']->common->phpgw_exit(False);
		}
		//@function setup_main_frame
		//@abstract This function is very large. Its the code that prebuilds all static data for the mainframe
		function setup_main_frame()
		{
			//Git our templates
		
			$this->template->set_file(array(
						'addressbook_names_t' => 'addressbook-js.tpl',
						'addressbook_names' => 'addressbook-js.tpl',
						'secofile_t' => 'addressbook-js-bits.tpl',
						'hidden_emails_t' => 'addressbook-js-bits.tpl',
						'selectboxes_t' => 'addressbook-js-bits.tpl'
						));

			/* DEPRECATED We initialize this switch that tells the code if any comes with data in it */
			$boxecoming=False;

			$this->searchbox  = $_POST['searchbox'] ? $_POST['searchbox'] : $_GET['searchbox'];
			$this->querycommand=$_POST['querycommand'] ? $_POST['querycommand'] : $_GET['querycommand'];
			if($this->querycommand=='cleanquery')//We have been ordered to clean the query
			{
				$this->searchbox="";
				$GLOBALS['phpgw']->session->appsession('jsuibook_sbox','email','');
			}
			elseif(!$this->searchbox)//wow, nothing in the searchbox, and weve been ordered to serve
						//....look for it in the cache
			{
					$this->searchbox=$GLOBALS['phpgw']->session->appsession('jsuibook_sbox','email');
			}
			/*      This is the View More checkbox */
			$this->viewmore = $_POST['viewmore'] ? $_POST['viewmore'] : $_GET['viewmore'];

			/*      
				The next three are, respectively, the selected To:,cc and bcc  selectboxes. We need them to remember if
				they something was selected on them and we, for example, clicked on a name and, thus, submited the form.
				We need to keep all values in this boxes. This is why the js code autoselects all of the options
				just before submiting. BTW, this should come in post allways but its a good practice to allways try and 
				get from both. Good thing about get_var in 0.9.15.
			 */
			$this->toselectbox=$_POST['toselectbox']? $_POST['toselectbox'] : $_GET['toselectbox'];
			$this->ccselectbox=$_POST['ccselectbox']? $_POST['ccselectbox'] : $_GET['ccselectbox'];
			$this->bccselectbox=$_POST['bccselectbox']? $_POST['bccselectbox'] : $_GET['bccselectbox'];
			$this->nameselectbox  = $_POST['nameselect'] ? $_POST['nameselect'] : $_GET['nameselect'];
			//nameselect is the value of the first selection in the nameselectbox
			$this->nameselect = $this->nameselectbox[0];
			$this->start  = $_POST['start'] ? $_POST['start'] : $_GET['start'];
			$this->filter  = $_POST['filter'] ? $_POST['filter'] : $_GET['filter'];
			//To selectbox must be hidden,set variables acordinlgy....likewise for cc and bcc
			if($this->hideto)
			{
				$this->template->set_var('hidetoselectjs','1');
				$this->template->set_var('hideto_open',"<!-- ");
				$this->template->set_var('hideto_close'," --> ");
			}
			else
			{
				$this->template->set_var('hidetoselectjs','0');
				$this->template->set_var('hideto_open',"");
				$this->template->set_var('hideto_close'," ");
			}
			if($this->hidecc)
			{
				$this->template->set_var('hideccselectjs','1');
				$this->template->set_var('hidecc_open',"<!-- ");
				$this->template->set_var('hidecc_close'," --> ");
			}
			else
			{
				$this->template->set_var('hideccselectjs','0');
				$this->template->set_var('hidecc_open',"");
				$this->template->set_var('hidecc_close'," ");
			}
			if($this->hidebcc)
			{
				$this->template->set_var('hidebccselectjs','1');
				$this->template->set_var('hidebcc_open',"<!-- ");
				$this->template->set_var('hidebcc_close'," --> ");
			}
			else
			{
				$this->template->set_var('hidebccselectjs','0');
				$this->template->set_var('hidebcc_open',"");
				$this->template->set_var('hidebcc_close'," ");
			}
			//In the same idea, see if we really want the directory option turned on
			if(!$this->globaladduid)
			{
				$this->template->set_var('hide_directory_option_open',"<!--");
				$this->template->set_var('hide_directory_option_close',"-->");

			}
			else
			{
				$this->template->set_var('hide_directory_option_open',"");
				$this->template->set_var('hide_directory_option_close',"");
			}
			//Set the template vars as needed to remember the selected filter in
			//the filter dropdown (right besides the autocomplete textbox)
			switch($this->filter)
			{
				case "none":
				{
					$this->template->set_var('global_is_selected',"selected");
					$this->template->set_var('mine_is_selected',"");
					$this->template->set_var('private_is_selected',"");
					$this->template->set_var('directory_is_selected',"");
					break;
				}
				case "private":
				{
					$this->template->set_var('mine_is_selected');
					$this->template->set_var('global_is_selected',"");
					$this->template->set_var('private_is_selected',"selected");
					$this->template->set_var('directory_is_selected',"");
					break;
				}
				case "user_only":
				{
					$this->template->set_var('private_is_selected',"");
					$this->template->set_var('mine_is_selected'," selected");
					$this->template->set_var('global_is_selected',"");
					$this->template->set_var('directory_is_selected',"");
					break;
				}
				case "directory":
				{
					$this->template->set_var('directory_is_selected',"selected");
					$this->template->set_var('mine_is_selected',"");
					$this->template->set_var('private_is_selected',"");
					$this->template->set_var('global_is_selected',"");
					break;
				}
				
				default:
				{
					$this->template->set_var('private_is_selected',"");
					$this->template->set_var('mine_is_selected',"");
					$this->template->set_var('global_is_selected',"");
					$this->template->set_var('directory_is_selected',"");
					break;

				}
			}
			//Get our category id
			$this->cat_id  = $_POST['cat_id'] ? $_POST['cat_id'] : $_GET['cat_id'];
			//print "<br /> ".$this->cat_id;
			//Check if we have to check in the cache
			if($this->cat_id == -1)
			{
				//Supposedly, the user wants us to show nothing, no query being made.
				//BUT, we check if we have cache that needs has another value
				$this->cat_id=$GLOBALS['phpgw']->session->appsession('jsuibook_catid','email');
				if(!$this->cat_id)//if we still have nothing
				{
					//Then we believe the user wants no query, otherwise, we used
					//the cached cat_id
					$this->cat_id=-1;
				}
			}
			if(!$this->cat_id)
			{
				$this->cat_id=$GLOBALS['phpgw']->session->appsession('jsuibook_catid','email');
				
			}
			//When the category id is -2, this means we selected all
			$this->cat_id = ($this->cat_id == -2)?"":$this->cat_id;
			//SPecial Personal category means filter=mine, catid=none
			if($this->cat_id == -3)
			{
				$this->filter="user_only";
				$this->cat_id="";
			}
			//The order query field...NOT USED ATM
			$this->order  = $_POST['order'] ? $_POST['order'] : $_GET['order'];
			//So we remember the autocomplete's value
			$this->searchautocomplete  = $_POST['searchautocomplete'] ? $_POST['searchautocomplete'] : $_GET['searchautocomplete'];
			if(!$this->searchautocomplete&&($this->querycommand != 'cleanquery'))//again, not found in the vars, look for it in cache
			{
				$this->searchautocomplete=$GLOBALS['phpgw']->session->appsession('jsuibook_acbox','email');
			}
			elseif($this->querycommand == 'cleanquery')//wants us to clean the cache
			{
				$this->searchautocomplete="";
				$GLOBALS['phpgw']->session->appsession('jsuibook_acbox','email','');
			}
			//The sort field ... NOTUSED ATM
			$this->sort = $_POST['sort'] ? $_POST['sort'] : $_GET['sort'];
			//Starnge.... i have no idea what does this do
			$catid_string=($this->cat_id) ? "cat_id=".$this->cat_id : "";
			
			/*      We need to get some preferences here so we can set the width the addressbook will have */
			$this->template->set_var('widget_font_size',$this->fontsizes[$this->scrsize]);

			/*      Block initialization... u see why i got the previous variables first dont you? */
			$this->template->set_block('addressbook_names_t','addressbook_names','list');
			$this->template->set_block('hidden_emails_t','B_hidden_emails_list','V_hidden_emails_list');
			$this->template->set_block('selectboxes_t','B_toselectbox','V_toselectbox');
			$this->template->set_block('selectboxes_t','B_ccselectbox','V_ccselectbox');
			$this->template->set_block('selectboxes_t','B_bccselectbox','V_bccselectbox');
			
			/*      Normal stuff and setting of template vars as they come  */
			$this->template->set_var('lang_addressbook_action',lang('Address book'));
			$this->template->set_var('main_form_name',"mainform");
			$this->template->set_var('lang_search',lang('Search'));
			$this->template->set_var('searchbox_value',(($searchbox) ? $searchbox : ""));
			$this->template->set_var('search_ac_value',(($this->searchautocomplete) ? $this->searchautocomplete : ""));
			//Label settings------------
			//for columns!
			$this->template->set_var('first_column_label',lang($this->first_column_label));
			$this->template->set_var('second_column_label',$this->second_column_label);
			$this->template->set_var('third_column_label',$this->third_column_label);
			//for buttons!
			$this->template->set_var('search_button_value',$this->search_button_value);
			$this->template->set_var('to_button_value',$this->to_button_value);
			$this->template->set_var('cc_button_value',$this->cc_button_value);
			$this->template->set_var('bcc_button_value',$this->bcc_button_value);
			$this->template->set_var('cancel_button_value',$this->cancel_button_value);
			$this->template->set_var('done_button_value',$this->done_button_value);
			$this->template->set_var('remove_button_value',$this->remove_button_value);
			$this->template->set_var('none_button_value',$this->none_button_value);
			$this->template->set_var('selected_category_name',$this->selected_category_name);
			//for query command dropdown!
			$this->template->set_var('select_query_command',$this->select_query_command);
			$this->template->set_var('go_query_command',$this->go_query_command);
			$this->template->set_var('clear_query_command',$this->clear_query_command);
			$this->categoryobject=CreateObject('phpgwapi.categories');
			$this->categoryobject->app_name = 'addressbook';
			$cat_name=$this->categoryobject->return_single($this->cat_id);
			$this->selected_cat_name=$cat_name[0]['name'];
			if($this->selected_cat_name)
			{
				
				$this->template->set_var('selected_cat_name',$this->selected_cat_name);
			}
			else
			{
				$this->template->set_var('selected_cat_name','');
				
			}
			$this->template->set_var('selected_cat_name',$this->selected_cat_name);
			$this->template->set_var('selected_cat_value',($this->cat_id=="")?-2:$this->cat_id);
			//End Label setting--------------
			$this->template->set_var('search_action',$GLOBALS['phpgw']->link('/'.$GLOBALS['phpgw_info']['flags']['currentapp'].'/addressbook-js.php',"sort=$this->sort&order=$this->order&filter=$this->filter&start=$this->start&cat_id=$this->cat_id"));
         		$this->template->set_var('query',$query);
	         	$this->template->set_var('order',$order);
	         	$this->template->set_var('searchbox_value',$this->searchbox);
	         	$this->template->set_var('viewmore_checked',($this->viewmore ? "checked" :""));
			$this->template->set_var('include_link',$GLOBALS['phpgw']->link("/email/inc/selectboxes.js"));
//			//Try and get the <option> string for the categories form cache
//			$this->catlist=$GLOBALS['phpgw']->session->appsession('jsuibook_catlist','email');
//			//We dont have none
//			if(!$this->catlist)
//			{
				//We get it from the category class and ...
				$this->categoryobject=CreateObject('phpgwapi.categories');
				$this->categoryobject->app_name = 'addressbook';
				$this->catlist=  str_replace( '&nbsp;&lt;' . lang('Global') . '&nbsp;' . lang($this->categoryobject->app_name).'&gt;'
							,'',$this->categoryobject->formated_list('select','all',$this->cat_id,'True'));
				//....save it in the cache
//				$GLOBALS['phpgw']->session->appsession('jsuibook_catlist','email',$catlist);
//			}
			$this->template->set_var('cats_list',
						$this->catlist
					);

			$this->template->set_var('lang_select',lang('Select'));
			$this->template->set_var('main_form_action',
					$GLOBALS['phpgw']->link(
						'/index.php',
						array(
							'menuaction'	=> "email.uijsaddressbook.show_mainframe",
							'sort'          => $this->sort,
							'order'         => $this->order,
							'filter'        => $this->filter,
							'start'         => $this->start,
							'query'         => $this->query,
							'cat_id'        => $this->cat_id,
							'hideto'	=> $this->hideto,
							'hidecc'	=> $this->hidecc,
							'hidebcc'	=> $this->hidebcc
						     )
						)
					);
			//Try and remember what values where selected so that we can put them back in the boxes as needed
			$GLOBALS['phpgw']->session->appsession('jsuibook_sbox','email',$this->searchbox);
			$GLOBALS['phpgw']->session->appsession('jsuibook_acbox','email',$this->searchautocomplete);
			$GLOBALS['phpgw']->session->appsession('jsuibook_catid','email',$this->cat_id);
			//The contactquery to send to the bo class constructor. Look into the bo class
			//for a discussion of this parameter
			$contactquery=($this->cat_id==-1) ?"":array('start' => $this->start,
							    'order' => $this->order,
							    'categories' => ($this->cat_id<0)?"":$this->cat_id,
							    'filter' => ($this->filter) ? $this->filter:"none",
							    'query' => $this->searchbox,
							    'sort' => $this->sort,
							    'directory_uid' => $this->globaladduid
							    );
	//		print "<br />";
	//		print_r($contactquery);		    
					
			
				//This means the nameselect box was clicked to find out info for a given user
				//So, no need to requery the database, the bo class already knows what the names are
				$this->bo=CreateObject("email.bojsaddressbook",$contactquery);
		}
		//@function show_mainframe
		//@abstract Actual public function that outputs the main frame
		//@discussion
		//This is in need of serious chaneg, i inherited most of this code from the previous
		//addressbook and really its not meant to do the same thing....
		function show_mainframe()
		{
			//Initialize the static data ....
			$this->setup_main_frame();
			//Get ourselves the result from the query made in setup_main_frame
			$entries=$this->bo->result;
			//Go and parse the destinationboxes first
			$this->show_destboxes();
			//Iterate from the result, see if we dont need
			//to output anything that was outputed in the selectboxes
			for ($i=0;$i<count($entries);$i++)
			{
				$id=$entries[$i]['id'];
				if(trim($entries[$i]['fn'])== '')
				{
					$firstname=$entries[$i]['n_given'];
					$lastname=$entries[$i]['n_family'];
					$fullname=$firstname." ".$lastname;
				}
				else
				{
					$fullname=$entries[$i]['fn'];
				}
				
				// --------------------- template declaration for list records --------------------------
				//if it went into the other boxes, we dont let him be in the select from box
				if((!$this->toselectbox[$id]) && (!$this->ccselectbox[$id]) && (!$this->bccselectbox[$id]))
				{
					$this->template->set_var('name_option_value',$id);
					$this->template->set_var('name_option_name',$fullname);
					$this->template->parse('list','addressbook_names',True);
					//Remember selected
					if($this->nameselect)
					{
						if($this->nameselect==$id)
						{
							$this->template->set_var('name_option_selected'," selected");
						}
						else
						{
							$this->template->set_var('name_option_selected',"");
						}
					}
				}
				else
				{
					$this->template->set_var('name_option_selected',"");

				}
//				$this->template->set_var('hidden_value',$email);
//				$this->template->set_var('hidden_name',"emails[$id]");
//				$this->template->parse('V_hidden_emails_list','B_hidden_emails_list',True);
			}
			// --------------------------- end record declaration ---------------------------
			$this->template->set_var('lang_done',lang('Done'));
			$this->template->parse('out','addressbook_names_t',True);
			$this->template->p('out');
			$GLOBALS['phpgw']->common->phpgw_exit();





		}

		//@function show_destboxes
		//@abstract This function outputs the destination boxes. 
		//@access private
		//@discussion this private function prints the destination boxes content
		//This functions queries the bo class for the destination boxes it has
		//cached. It then proceeds to parse that data out by templates
		//Importantly, this calss takes good care of outputing hidden email[] fields
		//for every element. Its important because the js frontend uses this to
		//naturaly put the email into the compose textboxes
		function show_destboxes()
		{
			//Make shure this guy doesnt go into the to,cc or bcc selectboxes
			//it expects the templates are well initialized as far as the destboxes blocks go
			$this->bo=(isset($this->bo)) ? $this->bo : CreateObject("email.bojsaddressbook");
			$selectboxes=$this->bo->get_destboxes();
			//print_r($selectboxes);
			//print_r($this->bo->result);
			
			if(is_array($selectboxes['toselectbox']) && (!$this->hideto))
			{
			reset($selectboxes['toselectbox']);
			
					while(list($k,$pair)=each($selectboxes['toselectbox']))
					{
						list($id,$value)=each($pair);
						$this->toselectbox[$id]=$value;
						$this->template->set_var('toselvalue',$id);
						$this->template->set_var('toselname',$value);
						$this->template->set_var('tosel_is_selected',"");
						$this->template->set_var('hidden_value',$pair['email']);
						$this->template->set_var('hidden_name',"emails[$id]");
						$this->template->parse('V_hidden_emails_list','B_hidden_emails_list',True);
						$this->template->parse('V_toselectbox','B_toselectbox',true);
					}
			}
			if(is_array($selectboxes['ccselectbox']) && (!$this->hidecc))
			{
			reset($selectboxes['ccselectbox']);
			
					while(list($k,$pair)=each($selectboxes['ccselectbox']))
					{
						list($id,$value)=each($pair);
						$this->ccselectbox[$id]=$value;
						$this->template->set_var('ccselvalue',$id);
						$this->template->set_var('ccselname',$value);
						$this->template->set_var('ccsel_is_selected',"");
						$this->template->set_var('hidden_value',$pair['email']);
						$this->template->set_var('hidden_name',"emails[$id]");
						$this->template->parse('V_hidden_emails_list','B_hidden_emails_list',True);
						$this->template->parse('V_ccselectbox','B_ccselectbox',true);
					}
			}
			if(is_array($selectboxes['bccselectbox']) && (!$this->hidebcc))
			{
			reset($selectboxes['bccselectbox']);
			
					while(list($k,$pair)=each($selectboxes['bccselectbox']))
					{
						list($id,$value)=each($pair);
						$this->bccselectbox[$id]=$value;
						$this->template->set_var('bccselvalue',$id);
						$this->template->set_var('bccselname',$value);
						$this->template->set_var('bccsel_is_selected',"");
						$this->template->set_var('hidden_value',$pair['email']);
						$this->template->set_var('hidden_name',"emails[$id]");
						$this->template->parse('V_hidden_emails_list','B_hidden_emails_list',True);
						$this->template->parse('V_bccselectbox','B_bccselectbox',true);
					}
			}
			

		}
		//@function set_detboxes
		//@access public
		//@abstract Sets the destination boxes valeus in the boclass
		//outputs email,keys and names js arrays for the js framwork to use
		//@discussion this public function gets called from a secondary frame by uri to 
		//set the values for the destination boxes..... it expects
		//the destination boxes options to come in through GPC vars
		function set_destboxes()
		{
			
			$this->toselectbox=$_POST['toselectbox']? $_POST['toselectbox'] : $_GET['toselectbox'];
			$this->ccselectbox=$_POST['ccselectbox']? $_POST['ccselectbox'] : $_GET['ccselectbox'];
			$this->bccselectbox=$_POST['bccselectbox']? $_POST['bccselectbox'] : $_GET['bccselectbox'];
			$deleted=$_POST['deleted']? $_POST['deleted'] : $_GET['deleted'];
			$forget_after=$_POST['forget_after']? $_POST['forget_after'] : $_GET['forget_after'];
			$this->bo=CreateObject("email.bojsaddressbook");
		
			$destboxes=array(
					"toselectbox" => $this->toselectbox,
					"ccselectbox" => $this->ccselectbox,
					"bccselectbox" => $this->bccselectbox
			     );
			$this->bo=CreateObject("email.bojsaddressbook","","");
			
			//leftover will hold any info that has not already been outputed
			//hint: emails are in a hidden field, if we are asyncronically calling this
			//	we need to return a js array with the missing emails and stuff
			//	so the caller JS can update the selectboxes as needed
			$leftover=$this->bo->set_destboxes($destboxes,$deleted);
			$hide["toselectbox"]=$this->hideto;
			$hide["ccselectbox"]=$this->hidecc;
			$hide["bccselectbox"]=$this->hidebcc;
			if($leftover)
			{
				
				while(list($boxk,$boxar)=each($leftover))
				{
					if(is_array($boxar) && (count($boxar) > 0))
					{
						if(!($hide[$boxk]==1) )
						{
							reset($boxar);

							while(list($id,$namemail)=each($boxar))
							{
								$jsdestboxes[$boxk."_keys"][]=$id;
								$jsdestboxes[$boxk."_email"][]=$namemail['email'];
								$jsdestboxes[$boxk."_name"][]=$namemail['name'];
							}
						}
					}
				}
			
				$arrays=$this->ary_to_js($jsdestboxes);
			}
			print $this->final_js($arrays,"window.parent.destboxes_set();");
			if($forget_after==1)
				$this->bo->forget_destboxes();
			$GLOBALS['phpgw']->common->phpgw_exit();			
		}
		//@function forget_destboxes
		//@access public
		//@abstract This function makes the bo class forget all destboxes data
		function
		forget_destboxes()
		{
			$this->bo=CreateObject("email.bojsaddressbook");
			$this->bo->forget_destboxes();

		}
		//@function forget_all
		//@access public
		//@abstract This function makes the bo class forget everything in cache
		function
		forget_all($non_interactive="")
		{
			
			$this->bo=CreateObject("email.bojsaddressbook");
			$this->bo->forget_destboxes();
			$this->bo->forget_query();
			//forget our own cache
			$GLOBALS['phpgw']->session->appsession('jsuibook_sbox','email',"");
			$GLOBALS['phpgw']->session->appsession('jsuibook_acbox','email',"");
			$GLOBALS['phpgw']->session->appsession('jsuibook_catid','email','');
			if($non_interactive=="")
			{
				print $this->final_js("","window.parent.all_forgoten();");
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			
		}
		//@function show_userdata
		//@access public
		//@abstract this function queries the bo class for the given
		//id field in the addressbook
		//@discussion
		//We need to drop all this userdata talk, this are not users, they are
		//addressbook entries. This expects the id to the addressbook record
		//to come in through the nameselect userdata field
		//If it gets it, it will query the bo class for its data
		//It will then make a js string and the corresponding call to the frameset to
		//tell the mainframe userdata has arrived
		function show_userdata()
		{
			
			$this->template->set_file(array(
						'userdata_t' => 'addressbook-js-bits.tpl'
						));
			$this->bo=CreateObject("email.bojsaddressbook");
			$this->nameselect  = $_POST['nameselect'] ? $_POST['nameselect'] : $_GET['nameselect'];
			$data = $this->bo->recordinfo($this->nameselect);
			if(!$data)
			{
				exit("user not found");
			}
			$this->template->set_block('userdata_t','B_addressbook_record_inner');
			$largest=0;
			while(list($k,$v)=each($this->field_trans))
			{
				$actualsize=strlen(lang($v));
				if($actualsize > $largest)
				{
					$largest=$actualsize;
				}
			}
			reset($data);
			$counter=0;
			while(list($k,$v)=each($data))
			{
				if($this->field_trans[$k] && strlen($v))
				{
					$this->template->set_var('field_name',lang($this->field_trans[$k]));
					$this->template->set_var('space_to_fit',str_pad("",$largest-strlen(lang($this->field_trans[$k]))));
					$this->template->set_var('field_value',$v);
					$fulst=$this->template->get_var('B_addressbook_record_inner');
					$actualsize=strlen($fulst);
					$largestfull=($largestfull < $actualsize) ? $actualsize : $largestfull;
					$this->template->parse('V_innerrecord','B_addressbook_record_inner',true);
					$counter++;
				}
			}
			$this->template->parse('out','V_innerrecord',True);
			$str=$this->template->get('out');
			print $this->final_js("var userdata_rows; 
						userdata_rows=".($counter+2)."; 
						var userdata_cols; 
						userdata_cols=".$largestfull.";
						var userdata; 
						userdata=\"".rawurlencode($str)."\";"
						,"window.parent.userdata_got();");
			$GLOBALS['phpgw']->common->phpgw_exit();
		}
		//@function ary_to_js
		//@access private
		//@discussion The idea is to have an array whose keys will tell us the varname (js)
		//and, those will point to the actual array to be converted
		//
		function ary_to_js($ary)
		{
			//print_r($ary);
			reset($ary);
			while(list($k,$v)=each($ary))
			{
				if(is_array($v) && (count($v)>0))
				{
					$ret=$ret."var ".$k.";";
					$ret=$ret." $k = new Array(".count($v).");";
					while(list($ak,$av)=each($v))
					{
						$ret=$ret.$k.'['.$ak."]=\"".$av."\";"; 
					}
				}
			}
			return $ret;
		}
		//@function final_js
		//@access private
		//@param innerstring Js code to be put within script tags
		//@param functioncall the name of the function to call onLoad
		//@discussion This builds the pages of the secondary frame
		//As you can see, its prepared to accept some js in its
		//innerstring parameter to be inserted in the middle of script tags
		//Also, it accepts a string that is supposed to be the javascript call to the frameset function
		//that tells the js framework we have outputed the data it queried for.
		function final_js($innerstring,$functioncall)
		{
			$retstr="<html>
				<head>
				<script language=\"javascript\">";
					
			$close="</script>
				</head>
				<body onLoad=\"$functioncall\">
				</body>
				</html>";
			return $retstr.''.$innerstring.''.$close;
					
		}
	}
