<?php
	/**
	* Addressbook Chooser
	* @author Alex Borges <alex@sogrp.com>
	* @author Dave Hall <dave.hall@mbox.com.au>
	* @author Gerardo Ramirez <gramirez@grupogonher.com>
	* @copyright Copyright (C) Gerardo Ramirez
	* @copyright Portions Copyright (C) 2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id$
	* @internal Inherited some code from the addressbook.php file
	*/

	/**
	* Addressbook Chooser
	* 
	* @package phpgwapi
	* @subpackage gui
	* This class will handle all user interaction logic.
	* ONLY USER INTERACTION. This means that this class has practically no
	* state except for the categories cache. What im trying to say here is
	* that this class does not query, does not call the contacts backend at all
	* it only visually represents whatever the bo class has in its state.
	*
	* Now, for how this works:
	* Look at the templates. There is a frameset template that decares two frames.
	* The mainframe, and the secondary frame. 
	*
	* The frameset:
 	* The frameset template is the main startpoint for this whole system. This
	* template declares the main and secondary frames. More importantly, this frame
	* declares all functions that are events in the framework. Look into the comments 
	* in the frameset template to understand this event framework.
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
	* to the mainframe. The whole idea is that we serve back data in js and tell
	* the main frame its data is here by sending a call by the onLoad event (which is
	* called just after we finish passing our data.
	*/
	class phpgwapi_uijsaddressbook
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
		
		//@param get_order array signifying the order in which to search for GET/POST vars in get_var
		var $get_order;
		//START The next parameters are public and customize the look of the js addressbook
		//@param title The title of the js addressbook window
		var $title="Corporate Addressbook GUI";
		//@param first_column_label What to show as text at the top of the first column
		var $first_column_label="Select Categories";
		//@param second_column_label What to show as text at the top of the second column
		var $second_column_label="Select Destination";
		//@param third_column_label What to show as text at the top of the third column
		var $third_column_label="Destination Options";
		//@param search_button_value The value to show as "label" for the search button
		var $search_button_value="Search";
		//@param top_dest_button_value The value to show as "label" for the to button
		var $top_dest_button_value="to";
		//@param middle_dest_button_value The value to show as "label" for the cc button
		var $middle_dest_button_value="cc";
		//@param bottom_dest_button_value The value to show as "label" for the cc button
		var $bottom_dest_button_value="bcc";
		//@param cancel_button_value The value to show as "label" for the cancel button
		var $cancel_button_value="Cancel";
		//@param done_button_value The value to show as "label" for the done button
		var $done_button_value="Done";
		//@param remove_button_value The value to show as "label" for the remove button
		var $remove_button_value="Remove";
		//@param bottom_dest_button_value The value to show as "label" for the bcc button
		var $none_button_value="None";
		//@param update_opener The user of the jsaddressbook wants us to output a 
		//got_contact_ids event he created in his application. This function MUST
		//be declared in javascript directly in the page where they called
		//window.open to open the jsaddressbook
		var $update_opener=0;
		//@param globaluid Okay, this is a very strange feature but i need it for my own deployment
		//if this value is set to something, an extra filter option will appear. It will
		//say global directory. My personal way to doing the global directory trick
		//was to make a user called addressmaster, who exports his ontacts to all
		//this valueshould be the addressmaster's uid for your deployment
		var $systemfilter=0;
		//@param field_trans
		//This array converts field names of the contact backend (or the bo class for that matter)
		//to english so they can be passed through a lang call to be shown to the user
		var $field_trans = "in_constructor"; 

		var $wait_image_path = "in_constructor";
		
		var $query;

		//@function uijsaddressbook constructor
		//@discussion
		//This is the main initializator. Here, we output the mainframe. Tats all we do. Its an important function
		//because the links for the events of the secondaryframe are built and presented to the browser
		//so that when the mainframe triggers an event, it will call some functions in the frameset (its window.parent)
		//And when the secondary frame returns from this public functions (which are all declared and defined in this file)
		//It will also call some of the functions in the frameset.
		//@param update_opener
		//If this is true, the frameset will set the {update_opener} template tag to
		//true, and the js framework will call an got_contact_ids method in the opening
		//window, which is the one where the window.open() function was called to pop up
		//the jsaddressbook
		function __construct($update_opener=false)
		{
			//Grab data that MUST be propagated through all frames	
			//Post Allways has precedence for good reason.
			$this->hideto	= phpgw::get_var('hideto');
			$this->hidecc	= phpgw::get_var('hidecc');
			$this->hidebcc	= phpgw::get_var('hidebcc');
			$this->hideto	= $this->hideto ;
			$this->hidecc	= $this->hidecc  ;
			$this->hidebcc	= $this->hidebcc ;
			$this->viewmore	= phpgw::get_var('viewmore');
			$this->nameselect 	= phpgw::get_var('nameselect');
			$this->cat_id	= phpgw::get_var('cat_id');
			$this->wait_image_path	= $GLOBALS['phpgw']->common->image('phpgwapi','wait');
			$destboxes		= phpgw::get_var('big_select');
			
			parse_str($destboxes,$destboxes);
			if ( is_array($destboxes) && count($destboxes) )
			{
				$this->toselectbox	= $destboxes['toselectbox'];
				$this->ccselectbox	= $destboxes['ccselectbox'];
				$this->bccselectbox	= $destboxes['bccselectbox'];
			}
			else
			{	
				$this->toselectbox	= phpgw::get_var('toselectbox');
				$this->ccselectbox	= phpgw::get_var('ccselectbox');
				$this->bccselectbox	= phpgw::get_var('bccselectbox');
			}
			$this->searchbox		= phpgw::get_var('searchbox');
			$this->querycommand		= phpgw::get_var('querycommand');
			$this->nameselectbox	= phpgw::get_var('nameselect');
			$this->order			= phpgw::get_var('order');
			$this->searchautocomplete	= phpgw::get_var('searchautocomplete');
			$this->viewmore			= phpgw::get_var('viewmore');
			$this->start			= phpgw::get_var('start');
			$this->sort				= phpgw::get_var('sort');
			$this->filter			= phpgw::get_var('filter');
			$this->inquery			= phpgw::get_var('in');
			$this->sel_all_cat		= phpgw::get_var('sel_all_cat');
			if(!$update_opener)
			{
				$this->update_opener = phpgw::get_var('update_opener');	
			}
			$this->update_opener=($this->update_opener ? '1' : '0');
			
		}
		function lang_all()
		{	
			//Lang The customizable 'look' parameters
			$this->title = $GLOBALS['phpgw']->lang($this->title);
			$this->first_column_label=$GLOBALS['phpgw']->lang($this->first_column_label);
			$this->second_column_label=$GLOBALS['phpgw']->lang($this->second_column_label);
			$this->third_column_label=$GLOBALS['phpgw']->lang($this->third_column_label);
			$this->search_button_value=$GLOBALS['phpgw']->lang($this->search_button_value);
			$this->top_dest_button_value=$GLOBALS['phpgw']->lang($this->top_dest_button_value);
			$this->middle_dest_button_value=$GLOBALS['phpgw']->lang($this->middle_dest_button_value);
			$this->bottom_dest_button_value=$GLOBALS['phpgw']->lang($this->bottom_dest_button_value);
			$this->cancel_button_value=$GLOBALS['phpgw']->lang($this->cancel_button_value);
			$this->done_button_value=$GLOBALS['phpgw']->lang($this->done_button_value);
			$this->remove_button_value=$GLOBALS['phpgw']->lang($this->remove_button_value);
			$this->none_button_value=$GLOBALS['phpgw']->lang($this->none_button_value);
			//$this->debug_all();

		}
		function debug_all()
		{
			$debstring=$debstring. "<br>\n<b> All arround Values<br>\n";
			$debstring=$debstring. "<br>\n\$this->get_order= ".$this->get_order;
			$debstring=$debstring. "<br>\n\$this->hideto = ".$this->hideto ;
			$debstring=$debstring. "<br>\n\$this->hidecc = ".$this->hidecc ;
			$debstring=$debstring. "<br>\n\$this->hidebcc  = ".$this->hidebcc  ;
			$debstring=$debstring. "<br>\n\$this->hideto = ".$this->hideto ;
			$debstring=$debstring. "<br>\n\$this->hidecc = ".$this->hidecc ;
			$debstring=$debstring. "<br>\n\$this->hidebcc  = ".$this->hidebcc  ;
			$debstring=$debstring. "<br>\n\$this->viewmore  = ".$this->viewmore  ;
			$debstring=$debstring. "<br>\n\$this->nameselect  = ".$this->nameselect  ;
			$debstring=$debstring. "<br>\n\$this->cat_id= ".$this->cat_id;
			$debstring=$debstring. "<br>\n\$this->toselectbox= ".$this->toselectbox;
			$debstring=$debstring. "<br>\n\$this->ccselectbox= ".$this->ccselectbox;
			$debstring=$debstring. "<br>\n\$this->bccselectbox= ".$this->bccselectbox;
			$debstring=$debstring. "<br>\n\$this->searchbox= ".$this->searchbox;
			$debstring=$debstring. "<br>\n\$this->querycommand= ".$this->querycommand;
			$debstring=$debstring. "<br>\n\$this->nameselectbox  = ".$this->nameselectbox  ;
			$debstring=$debstring. "<br>\n\$this->order  = ".$this->order  ;
			//So we remember the autocomplete's value
			$debstring=$debstring. "<br>\n$this->searchautocomplete  = ".$this->searchautocomplete  ;
			/*      This is the View More checkbox */
			$debstring=$debstring. "<br>\n$this->viewmore = ".$this->viewmore ;
			$debstring=$debstring. "<br>\n$this->start  = ".$this->start  ;
			//The sort field ... NOTUSED ATM
			$debstring=$debstring. "<br>\n$this->sort = ".$this->sort ;
			$debstring=$debstring. "<br>\n$this->filter  = ".$this->filter."</b>"  ;
			return $debstring;

		}
		//@function setup_frameset
		//@abstract set default values for a bunch of visual stuff
		function setup_frameset()
		{
			//We create our template
			$this->setup_template();				
			$this->template->set_file(array(
						'mainframe_t' => 'addressbook-js-frameset.tpl'
						));
			//Set the charset (important for translations)
			$charset = 'utf-8';//$GLOBALS['phpgw']->translation->translate('charset');
			//Set your normal variables
			$this->template->set_var('charset',$charset);
			$this->template->set_var('title',$this->title);
		//	$this->template->set_var('bg_color',$GLOBALS['phpgw_info']['theme']['bg_color']);
			//Dont forget to include oour main javascript file
			$this->template->set_var('include_link',$GLOBALS['phpgw']->link("/phpgwapi/js/contacts/selectboxes.js"));
		//	$this->template->set_var('font',$GLOBALS['phpgw_info']['theme']['font']);
			$this->template->set_var('please_update_opener',$this->update_opener);
			//Get the hider values where needed
			//$this->hideto  = get_var('hideto',$this->get_order);
		//Set the link to the main frame...see, this is what im talking about
			//asd soon as this frameset is server to the browser it will go GET
			//the given url which is a call to the public function show_mainframe
			//declared and defined bellow....see what i mean?
			$this->template->set_var('mainframe_link', $GLOBALS['phpgw']->link(
													"/index.php",
													array(
													"menuaction"=>"phpgwapi.uijsaddressbook.show_mainframe",
													"viewmore" => "1",
													"cat_id" => $this->cat_id,
													"hideto" => $this->hideto,
													"hidecc" => $this->hidecc,
													"hidebcc" => $this->hidebcc,
													"in" => $this->inquery
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
													"menuaction"=>"phpgwapi.uijsaddressbook.show_userdata",
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
													"menuaction"=>"phpgwapi.uijsaddressbook.set_destboxes",
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
													"menuaction"=>"phpgwapi.uijsaddressbook.forget_all"
							 								)
													
													)
								);
			//Important to know our own screensize...
			$this->scrsize=$GLOBALS['phpgw_info']['user']['preferences']['email']['js_addressbook_screensize'];
		}
		//@abstract public function merely outputs the parsed frameset template
		//@function show
		//@abstract public function merely outputs the parsed frameset template
		function show()
		{
			$this->setup_frameset();
			$this->template->parse('out','mainframe_t',True);
			$this->template->p('out');
//			echo $this->debug_all();
			//$GLOBALS['phpgw']->common->phpgw_exit();
			// (angles) param False tells it now to try parse_navber_end in phpgwapi functions.inc.php, eliminates this php error:
			// PHP Fatal error:  Call to undefined function:  parse_navbar_end() in /phpgwapi/inc/footer.inc.php on line 62
			$GLOBALS['phpgw']->common->phpgw_exit(False);
		}
		//@function setup_template
		//@abstract Just creates de propper this->template object in the right way
		function setup_template()
		{
		
			$this->template=CreateObject('phpgwapi.template');
			//We set its root (we need to be called from other apps as well)
			//$this->template->set_root(PHPGW_SERVER_ROOT."/email/templates/".$GLOBALS['phpgw_info']['user']['preferences']['common']['template_set']);
			// (angles) fix suggested by Dave Hall allows email app compose page to be used with any template, 
			// fixes bug where email app compose page needed an (empty) template named dir in the email dir tree to use said named template 
			$this->template->set_root($GLOBALS['phpgw']->common->get_tpl_dir('phpgwapi'));
	
		}

		//@function setup_main_frame
		//@abstract This function is very large. Its the code that prebuilds all static data for the mainframe
		function setup_main_frame()
		{
			//Important to know our own screensize...
			$this->scrsize=$GLOBALS['phpgw_info']['user']['preferences']['email']['js_addressbook_screensize'];

			$this->lang_all();
			$this->setup_template();
			//Get our templates
			$this->template->set_file(array(
						'addressbook_names_t' => 'addressbook-js.tpl',
						'addressbook_names' => 'addressbook-js.tpl',
						'secofile_t' => 'addressbook-js-bits.tpl',
						'hidden_emails_t' => 'addressbook-js-bits.tpl',
						'selectboxes_t' => 'addressbook-js-bits.tpl'
						));

			/* DEPRECATED We initialize this switch that tells the code if any comes with data in it */
			$boxecoming=False;

		if($this->querycommand=='cleanquery')//We have been ordered to clean the query
			{
				$this->searchbox="";
				$GLOBALS['phpgw']->session->appsession('jsuibook_sbox','phpgwapi','');
			}
			elseif(!$this->searchbox)//wow, nothing in the searchbox, and weve been ordered to serve
						//....look for it in the cache
			{
					$this->searchbox=$GLOBALS['phpgw']->session->appsession('jsuibook_sbox','phpgwapi');
			}

			/*      
				The next three are, respectively, the selected To:,cc and bcc  selectboxes. We need them to remember if
				they something was selected on them and we, for example, clicked on a name and, thus, submited the form." 

				We need to keep all values in this boxes. This is why the js code autoselects all of the options
				just before submiting. BTW, this should come in post allways but its a good practice to allways try and 
				get from both. Good thing about get_var in 0.9.15.
			 */
			//nameselect is the value of the first selection in the nameselectbox
			$this->nameselect = isset($this->nameselectbox[0]) ? $this->nameselectbox[0] : '';
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
			if(!$this->systemfilter)
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
			//print "<br> ".$this->cat_id;
			//Check if we have to check in the cache
			if($this->cat_id == -1)
			{
				//Supposedly, the user wants us to show nothing, no query being made.
				//BUT, we check if we have cache that needs has another value
				$this->cat_id=$GLOBALS['phpgw']->session->appsession('jsuibook_catid','phpgwapi');
				if(!$this->cat_id)//if we still have nothing
				{
					//Then we believe the user wants no query, otherwise, we used
					//the cached cat_id
					$this->cat_id=-1;
				}
			}
			if(!$this->cat_id)
			{
				$this->cat_id=$GLOBALS['phpgw']->session->appsession('jsuibook_catid','phpgwapi');
				
			}
			//SPecial Personal category means filter=mine, catid=none
			if($this->cat_id == -3)
			{
				$this->filter="user_only";
				$this->cat_id="";
			}

			//When the category id is -2, this means we selected all
			$this->cat_id = ($this->cat_id == -2)?"-3":$this->cat_id;

			//The order query field...NOT USED ATM
			if(!$this->searchautocomplete&&($this->querycommand != 'cleanquery'))//again, not found in the vars, look for it in cache
			{
				$this->searchautocomplete=$GLOBALS['phpgw']->session->appsession('jsuibook_acbox','phpgwapi');
			}
			elseif($this->querycommand == 'cleanquery')//wants us to clean the cache
			{
				$this->searchautocomplete="";
				$GLOBALS['phpgw']->session->appsession('jsuibook_acbox','phpgwapi','');
			}
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
			$this->template->set_var('searchbox_value',(isset($searchbox) && ($searchbox) ? $searchbox : ""));
			$this->template->set_var('search_ac_value',(($this->searchautocomplete) ? $this->searchautocomplete : ""));
			//Label settings------------
			//for columns!
			
			$this->template->set_var('first_column_label',lang($this->first_column_label));
			$this->template->set_var('second_column_label',$this->second_column_label);
			$this->template->set_var('third_column_label',$this->third_column_label);
			$this->template->set_var('wait_img_path', $this->wait_image_path);
			//for buttons!
			$this->template->set_var('search_button_value',$this->search_button_value);
			$this->template->set_var('top_dest_button_value',$this->top_dest_button_value);
			$this->template->set_var('middle_dest_button_value',$this->middle_dest_button_value);
			$this->template->set_var('bottom_dest_button_value',$this->bottom_dest_button_value);
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
			$this->template->set_var('search_action',$GLOBALS['phpgw']->link('/'.$GLOBALS['phpgw_info']['flags']['currentapp'].'/addressbook-js.php',array('sort'=>$this->sort,'order'=>$this->order,'filter'=>$this->filter,'start'=>$this->start,'cat_id'=>$this->cat_id)));
		 		$this->template->set_var('query',$query);
			 	$this->template->set_var('order',$order);
			 	$this->template->set_var('searchbox_value',$this->searchbox);
			$this->template->set_var('viewmore_checked',($this->viewmore ? "checked" :""));
			//jarg-SOG s
			$this->template->set_var('sel_all_cat_checked',($this->sel_all_cat ? "checked" :""));
			//jarg-SOG e
			$this->template->set_var('include_link',$GLOBALS['phpgw']->link("/phpgwapi/js/contacts/selectboxes.js"));
			//We get it from the category class and ...
			$this->categoryobject=CreateObject('phpgwapi.categories');
			$this->categoryobject->app_name = 'addressbook';
			$this->catlist=str_replace( '&nbsp;&lt;' . lang('Global') . '&nbsp;' . lang($this->categoryobject->app_name).'&gt;'
					,'',$this->categoryobject->formated_list('select','all',$this->cat_id,'True'));
			//			}
			$this->template->set_var('cats_list',
						$this->catlist
					);

			$this->template->set_var('lang_select',lang('Select'));
			$this->template->set_var('main_form_action',
					$GLOBALS['phpgw']->link(
						'/index.php',
						array(
							'menuaction'	=> "phpgwapi.uijsaddressbook.show_mainframe",
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
			$GLOBALS['phpgw']->session->appsession('jsuibook_sbox','phpgwapi',$this->searchbox);
			$GLOBALS['phpgw']->session->appsession('jsuibook_acbox','phpgwapi',$this->searchautocomplete);
			$GLOBALS['phpgw']->session->appsession('jsuibook_catid','phpgwapi',$this->cat_id);
			//The contactquery to send to the bo class constructor. Look into the bo class
			//for a discussion of this parameter
			$contactquery=array('start' => $this->start,
						'order' => $this->order,
						'categories' => $this->cat_id,
						'filter' => ($this->filter) ? $this->filter:"none",
						'query' => $this->searchbox,
						'sort' => $this->sort,
						'directory_uid' => $this->systemfilter,
						'in' => $this->inquery
				);
			print "<BR>";
			//print_r($contactquery);		    
					
			
				//This means the nameselect box was clicked to find out info for a given user
				//So, no need to requery the database, the bo class already knows what the names are
				$this->bo=CreateObject("phpgwapi.bojsaddressbook",$contactquery);
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
			if(!is_array($entries))
			{
				$entries = array();
			}

			if($this->sel_all_cat)
			{
				$cat_name = $this->categoryobject->id2name($this->cat_id);
				$this->template->set_var('name_option_value','id_'.$this->cat_id);
				$this->template->set_var('name_option_name',$cat_name);
				$this->template->parse('list','addressbook_names');
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
				foreach($entries as $i => $data)
				//for ($i=0;$i<count($entries);$i++)
				{
					$id=$entries[$i]['contact_id'];
					
					if(trim($entries[$i]['per_full_name'])== '')
					{
						$firstname=$entries[$i]['per_first_name'];
						$lastname=$entries[$i]['per_last_name'];
						$fullname=$firstname." ".$lastname;
					}
					else
					{
						$fullname=$entries[$i]['per_full_name'];
					}
				
					// --------------------- template declaration for list records --------------------------
					//if it went into the other boxes, we dont let him be in the select from box
					if((!isset($this->toselectbox[$id]) || !$this->toselectbox[$id])
						&& (!isset($this->ccselectbox[$id]) || !$this->ccselectbox[$id])
						&& (!isset($this->bccselectbox[$id]) || !$this->bccselectbox[$id]))
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
						if($this->inquery)
						{
							$this->template->set_var('toselvalue',$id);
							$this->template->set_var('toselname',$fullname);
							$this->template->set_var('tosel_is_selected',"");
							$this->template->set_var('hidden_value',$entries[$i]['email']);
							$this->template->set_var('hidden_name',"emails[$id]");
							$this->template->parse('V_hidden_emails_list','B_hidden_emails_list',True);
							$this->template->parse('V_toselectbox','B_toselectbox',true);
						}
					}
					else
					{
						$this->template->set_var('name_option_selected',"");
						
					}
				}
			}
			// --------------------------- end record declaration ---------------------------
			$this->template->set_var('lang_done',lang('Done'));
			$this->template->parse('out','addressbook_names_t',True);
			$this->template->p('out');
			//print $this->debug_all();
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
			$this->bo=(isset($this->bo)) ? $this->bo : CreateObject("phpgwapi.bojsaddressbook");
			$selectboxes=$this->bo->get_destboxes();
			//print_r($selectboxes);
			//print_r($this->bo->result);
			
			if(is_array($selectboxes['toselectbox']) && (!$this->hideto))
			{
					reset($selectboxes['toselectbox']);
			
					foreach($selectboxes['toselectbox'] as $k => $pair)
					{
						//list($id,$value)=each($pair);
						$id = key($pair);
						$value = current($pair);
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
			
					foreach($selectboxes['ccselectbox'] as $k => $pair)
					{
						//list($id,$value)=each($pair);
						$id = key($pair);
						$value = current($pair);
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

				foreach($selectboxes['bccselectbox'] as $k => $pair)
				{
					//list($id,$value)=each($pair);
					$id = key($pair);
					$value = current($pair);
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
			
			$deleted = phpgw::get_var('deleted');
			$forget_after = phpgw::get_var('forget_after');
			$this->bo=CreateObject("phpgwapi.bojsaddressbook");
			$destboxes=array(
					"toselectbox" => $this->toselectbox,
					"ccselectbox" => $this->ccselectbox,
					"bccselectbox" => $this->bccselectbox
				 );
			//$this->bo=CreateObject("phpgwapi.bojsaddressbook","","");
			
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
				
				foreach($leftover as $boxk => $boxar)
				{
					if(is_array($boxar) && (count($boxar) > 0))
					{
						if(!($hide[$boxk]==1) )
						{
							reset($boxar);

							foreach($boxar as $id => $namemail)
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
			$this->bo=CreateObject("phpgwapi.bojsaddressbook");
			$this->bo->forget_destboxes();

		}
		//@function forget_all
		//@access public
		//@abstract This function makes the bo class forget everything in cache
		function
		forget_all($non_interactive="")
		{
			
			$this->bo=CreateObject("phpgwapi.bojsaddressbook");
			$this->bo->forget_destboxes();
			$this->bo->forget_query();
			//forget our own cache
			$GLOBALS['phpgw']->session->appsession('jsuibook_sbox','phpgwapi',"");
			$GLOBALS['phpgw']->session->appsession('jsuibook_acbox','phpgwapi',"");
			$GLOBALS['phpgw']->session->appsession('jsuibook_catid','phpgwapi','');
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
			$this->setup_template();
			$this->template->set_file(array(
						'userdata_t' => 'addressbook-js-bits.tpl'
						));
			if(strpos($this->nameselect, 'id_')!==false)
			{
				$cat_id = substr($this->nameselect, 3);
				$this->bo=(isset($this->bo)) ? $this->bo : CreateObject("phpgwapi.bojsaddressbook");
				$entries = $this->bo->get_persons_by_list($cat_id);
				
				$this->template->set_block('userdata_t','B_addressbook_record_inner');
				foreach($entries as $data)
				{
					$this->template->set_var('field_name',$data['name']);
					$this->template->set_var('space_to_fit',"");
					//$this->template->set_var('field_value','<'.$data['email'].'>q');
					$this->template->set_var('field_value',"");
					$fulst=$this->template->get_var('B_addressbook_record_inner');
					$actualsize=strlen($fulst);
					$largestfull=($largestfull < $actualsize) ? $actualsize : $largestfull;
					$this->template->parse('V_innerrecord','B_addressbook_record_inner',true);
					$counter++;
				}
			}
			else
			{
				$this->bo=CreateObject("phpgwapi.bojsaddressbook");
				$this->field_trans=$this->bo->contactsobject->contact_fields['showable'];
				$data = $this->bo->recordinfo($this->nameselect);
				if(!$data)
				{
					exit("user not found");
				}
				$this->template->set_block('userdata_t','B_addressbook_record_inner');
				$largest=0;
				//while(list($k,$v)=each($this->field_trans))
				if (is_array($this->field_trans))
				{
					foreach($this->field_trans as $k => $v)
					{
						$actualsize=strlen(lang($v));
						if($actualsize > $largest)
						{
							$largest=$actualsize;
						}
					}
				}
				reset($data);
				$counter=0;
				foreach($data as $k => $v)
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
			//reset($ary);
			//while(list($k,$v)=each($ary))
			foreach($ary as $k => $v)
			{
				if(is_array($v) && (count($v)>0))
				{
					$ret=$ret."var ".$k.";";
					$ret=$ret." $k = new Array(".count($v).");";
					//while(list($ak,$av)=each($v))
					foreach($v as $ak => $av)
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

