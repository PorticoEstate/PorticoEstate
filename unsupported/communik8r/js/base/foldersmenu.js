function folderMenu(strDiv)
{
	this.oMenu = document.getElementById(strDiv);
	this.timeMenuHide = 0;
	this.currentSelection = '';

	var oThis = this;
	
	this.init = function()
	{
		this._addListeners();
	}

	
	this.cancelHide = function(e)
	{
		clearTimeout(oThis.timerMenuHide);
	}
	
	this.clicked = function(e)
	{
		if(eventsLocked)
		{
			return false;
		}

		eventsLocked = true;
		
		if( !e ) //Fix Broken IE
		{
			var e = window.event;
		}

		if( e.target )
		{
			var oTarget = e.target;
		}
		else if( e.srcElement )
		{
			var oTarget = e.srcElement;
		}
		else
		{
			eventsLocked = false;
			return false;
		}
		window.alert('event received from: ' + oTarget.id.substr(13) + ' for ' + oThis.currentSelection);
		oThis.hideMenu();
		eventsLocked = false;
	}

	this.hideMenu = function()
	{
		oThis.oMenu.style.display = 'none';
	}

	this.mout = function(e)
	{
		if( !e ) //Fix Broken IE
		{
			var e = window.event;
		}

		if( e.target )
		{
			var oTarget = e.target;
		}
		else if( e.srcElement )
		{
			var oTarget = e.srcElement;
		}
		else
		{
			return false;
		}
		this.timerMenuHide = setTimeout('hideFolderMenu()', 1500);

		//Stop the bubble - else it kills all other events
		if( window.event ) //Crappy IE
		{
			e.cancelBubble = true;
		}
		else //W3C :)
		{
			e.stopPropagation();
		}
	}


	this.show = function(strTargetFolder)
	{
		var oTargetFolder = document.getElementById(strTargetFolder);
		oThis.oMenu.style.top = findPosY(oTargetFolder) + 'px';
		oThis.oMenu.style.left = findPosX(oTargetFolder) + (oTargetFolder.offsetWidth/2) + 'px';
		oThis.oMenu.style.position = 'absolute';
		oThis.oMenu.style.display = 'block';
		oThis.currentSelection = strTargetFolder;
	}

	this._addListeners = function()
	{
		if( this.oMenu.addEventListener )
		{
			this.oMenu.addEventListener('mouseout', this.mout, false);
			this.oMenu.addEventListener('mouseover', this.cancelHide, false);
			for(i=0; i < this.oMenu.childNodes.length; i++)//ULs
			{
				for(j=0; j < this.oMenu.childNodes.item(i).childNodes.length; j++)//LIs
				{
					this.oMenu.childNodes.item(i).childNodes.item(j).addEventListener('click', this.clicked, false);
				}
			}
		}
		else if( this.oMenu.attachEvent )
		{
			this.oMenu.attachEvent('onmouseout', this.mout);
			this.oMenu.attachEvent('onmouseover', this.cancelHide);
			for(i=0; i < this.oMenu.childNodes.length; i++)//ULs
			{
				for(j=0; j < this.oMenu.childNodes.item(i).childNodes.length; j++)//LIs
				{
					this.oMenu.childNodes.item(i).childNodes.item(j).attachEvent('click', this.clicked, false);
				}
			}
		}
	}
	this.init();
}

