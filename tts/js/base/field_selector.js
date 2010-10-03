		//make this method actually do something
		ygDDList.prototype.endDrag = function(e)
		{
			reStripe();
		}
		
		
		/**
		* Alternate the css class for an array of html elements
		*
		* @internal TODO Move to API base
		* @param array elms the elements to stripe
		* @param string on the css class name for the "on" elements
		* @param string off the css class name for the "off" elements
		*/
		function stripe(elms, on, off)
		{
			var regexOn = new RegExp(on);
			var regexOff = new RegExp(off);
			var regex = regexOn;
			
			var row = '';
			var rowDud = '';
			
			for ( var i = 0; i < elms.length; ++i )
			{
				row = ( i%2 ) ? on : off;
				rowDud = ( i%2 ) ? off : on;
				regex = ( i%2 ) ? regexOn : regexOff;
				
				if ( !elms[i].className.match(regex) )
				{
					removeClassName(elms[i], rowDud);
					addClassName(elms[i], row);
				}
			}
		}
		
		function reStripe()
		{
			stripe(document.getElementById('fields_list').getElementsByTagName('li'), 'row_on', 'row_off');
		}

		function moveBottom()
		{
			var parentNode = document.getElementById('fields_list');
			var elms = parentNode.getElementsByTagName('li');
			var lastElm = elms[elms.length - 1 ]; //footer is the last li, so we want to to be above  it
			
			selectedElms = findSelected(elms, 'highlight');
			elms = [];
			
			var elm;
			while ( selectedElms.length )
			{
				elm = selectedElms.pop();
				parentNode.insertBefore(elm, lastElm);
				lastElm = elm;
			}
			reStripe();
			return false;
		}
		
		function moveDown()
		{
			var parentNode = document.getElementById('fields_list');
			var elms = parentNode.getElementsByTagName('li');
			
			selectedElms = findSelected(elms, 'highlight');
			var lastElm = selectedElms[selectedElms.length - 1];
			elms = [];

			if ( lastElm.nextSibling )//make sure we don't fall off the edge
			{
				lastElm = lastElm.nextSibling;
			}
			if ( lastElm.nextSibling )//make sure we don't fall off the edge
			{
				lastElm = lastElm.nextSibling;
			}
			
			var elm;
			while ( selectedElms.length )
			{
				elm = selectedElms.pop();
				parentNode.insertBefore(elm, lastElm);
				lastElm = elm;
			}
			reStripe();
			return false;
		}

		function moveTop()
		{
			var parentNode = document.getElementById('fields_list');
			var elms = parentNode.getElementsByTagName('li');
			var firstElm = elms[1]; //header is the first li, so we want to skip it
			
			selectedElms = findSelected(elms, 'highlight');
			elms = [];
			
			var elm;
			while ( selectedElms.length )
			{
				elm = selectedElms.pop();
				parentNode.insertBefore(elm, firstElm);
				firstElm = elm;
			}
			reStripe();
			return false;
		}
		
		function moveUp()
		{
			var parentNode = document.getElementById('fields_list');
			var elms = parentNode.getElementsByTagName('li');
			var firstElm = elms[1];
			
			selectedElms = findSelected(elms, 'highlight');
			
			if ( selectedElms[0] != elms[1] ) //not already at top
			{
				firstElm = selectedElms[0].previousSibling;
			}

			elms = [];
			
			var elm;
			while ( selectedElms.length )
			{
				elm = selectedElms.pop();
				parentNode.insertBefore(elm, firstElm);
				firstElm = elm;
			}
			reStripe();
			return false;
		}
		

		function findSelected(elms, strSearch)
		{
			selectedItems = [];
			if ( !elms.length )
			{
				return selectedItems;
			}
			
			var regex = new RegExp(strSearch);
			
			for ( var i = 0; i < elms.length; ++i )
			{
				if ( elms[i].className.match(regex) )
				{
					selectedItems[selectedItems.length] = elms[i];
				}
			}
			return selectedItems;
		}

		function highlight(evnt)
		{
			var elm = YAHOO.util.Event.getTarget(evnt, true);
			
			if ( elm.tagName.toLowerCase() == 'input' 
				|| elm.tagName.toLowerCase() == 'img'
				|| elm.tagName.toLowerCase() == 'a' ) //don't let checkboxes or styled checkboxes fire it
			{
				return false;
			}
			
			if ( elm.tagName.toLowerCase() != 'li' )
			{
				while ( elm.tagName.toLowerCase() != 'li' )
				{
					elm = elm.parentNode;
				}
			}

			if ( evnt.ctrlKey )
			{
				addClassName(elm, 'highlight');
			}
			else if( evnt.shiftKey )
			{
				var hilite = false;
				var regex = new RegExp('highlight');
				var parent = elm.parentNode;
				for ( var i = 0; i < parent.childNodes.length; ++i)
				{
					if ( parent.childNodes[i].nodeType == 1 ) //only want HTML elements
					{ 
						if ( parent.childNodes[i].id == elm.id 
							|| parent.childNodes[i].className.match(regex) )
						{
							if ( !hilite )
							{
								addClassName(parent.childNodes[i], 'highlight');
								hilite = true;
								continue;
							}
							else if ( hilite )
							{
								addClassName(parent.childNodes[i], 'highlight');
								break;
							}
						}
						
						if ( hilite )
						{
							addClassName(parent.childNodes[i], ' highlight');
						}
					}
						
				}
			}
			else //normal click
			{
				var parent = elm.parentNode;
				for ( var i = 0; i < parent.childNodes.length; ++i)
				{
					if ( parent.childNodes[i].nodeType == 1 ) //only want HTML elements
					{ 
						if ( parent.childNodes[i].id == elm.id )
						{
							addClassName(parent.childNodes[i], 'highlight')
						}
						else
						{
							removeClassName(parent.childNodes[i], 'highlight')
						}
					}
				}
			}
		}

		var dd = [];
		function dragDropInit()
		{
			var i = 0;

			var fields = document.getElementById('fields_list').childNodes;
			for ( var j = 0; j < fields.length; ++j)
			{
				if ( fields[j].nodeType != 1 
					|| fields[j].tagName.toLowerCase() != 'li' 
					|| fields[j].id.substr(0, 7) == 'header_'
					|| fields[j].id.substr(0, 7) == 'footer_' )
				{
					continue;
				}
				
				dd[i++] = new ygDDList(fields[j].id);
				YAHOO.util.Event.addListener(fields[j], 'click', highlight);
			}
			
			dd[i++] = new ygDDListBoundary('header_fields');
			dd[i++] = new ygDDListBoundary('footer_fields');
			
			YAHOO.util.DDM.mode = YAHOO.util.DDM.INTERSECT;
		}
		
		function initPage()
		{
			InitialiseCheckboxes();
			InitialiseRadioboxes();
			dragDropInit();
		}
		YAHOO.util.Event.addListener(window, 'load', initPage);