/*
* news_admin Newsletter builder for phpGroupWare
* Written by Dave Hall
* Copyright 2005 Free Software Fondation Inc
* Sponsored by PRAV.asn.au
*/

	function addRecipient()
	{
		var oText = document.getElementById('cc_recipients_text');
		if ( oText.value )
		{
			var oOpt = document.createElement('option');
			oOpt.appendChild(document.createTextNode(oText.value));
			var oList = document.getElementById('cc_recipients');
			oList.appendChild(oOpt);
			//oList.options.sort(); //TODO: nice touch 
			oText.value = '';
		}
		return false;
	}

	function getNewsletterURL(iID, strTitle)
	{
		var strNewURL = strLinkURL.replace(/\*\*title\*\*/, strTitle.replace(/\s+/, '_').toLowerCase() );
		return strNewURL.replace(/\*\*id\*\*/, iID);
	}

	function nlbLoaded()
	{
	}

	function switchTab(oTarget)
	{
		if ( !document.all)//Dodgy Gecko detection
		{
			var iTab = oTarget.id.substr(7, oTarget.id.length);

			var oFCK = FCKeditorAPI.GetInstance('nl_content');

			if ( oFCK.EditMode == FCK_EDITMODE_WYSIWYG )
			{
				oFCK.SetStatus();
			}

			if ( iTab == 2 )
			{
				oFCK.SetStatus();
				oFCK.SetStatus();
			}
		}
	}
	
	/**
	* Toggle a story being availble in a news letter or not
	* @param int iStory the story ID
	*/
	function toggleStory(iStory)
	{
		var oFCK = FCKeditorAPI.GetInstance('nl_content');
		var oDOM = oFCK.EditorDocument;
		if ( document.getElementById('check_' + iStory).checked )
		{
			var strStory = document.getElementById('teaser_' + iStory).innerHTML;
			var strTitle = document.getElementById('title_' + iStory).innerHTML;
			oDiv = oDOM.createElement('div');
			oDiv.id = 'story_' + iStory;

			oH2 = oDOM.createElement('h2');
			oH2.innerHTML = strTitle;
			oDiv.appendChild(oH2);

			oP = document.createElement('p');
			oP.innerHTML = strStory;
			
			oP.appendChild(oDOM.createElement('br'));
			
			oAHref = document.createElement('a');
			oAHref.href = getNewsletterURL(iStory, strTitle);
			oAHref.appendChild(oDOM.createTextNode(oLang['read_more']));
			oP.appendChild(oAHref);

			oDiv.appendChild(oP);
			oDOM.getElementById('news').appendChild(oDiv);
		}
		else
		{
			var oElm2Remove = oDOM.getElementById('story_' + iStory);
			oDOM.body.removeChild(oElm2Remove);
		}
		
	}

	function removeSelected()
	{
		var oSelect = document.getElementById('cc_recipients');
		for ( i=0; i < oSelect.options.length; ++i)
		{
			if ( oSelect.options[i].selected )
			{
				oSelect.options[i] = null;
			}
		}
	}

	function selectAllBCC()
	{
		var oSelect = document.getElementById('cc_recipients');
		for ( i=0; i < oSelect.options.length; ++i)
		{
			oSelect.options[i].selected = true;
		}
		return true;
	}
