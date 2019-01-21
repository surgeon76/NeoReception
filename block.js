function Block(parentEl, doc)
{
	var key = Register(this);
	doc.SetView(this);
	
	var collapsed = true;
	var contentLoaded = false;
	
	var div = document.createElement('div');
	parentEl.appendChild(div);
	div.view = this;
	
	var divHeader = document.createElement('div');
	divHeader.style.border = '1px solid white';
	divHeader.style.fontWeight = 'bold';
	div.appendChild(divHeader);
	divHeader.style.cursor = 'pointer';
	divHeader.style.padding = '0px 0px';
	divHeader.tabIndex = '0';
	
	var divContent = document.createElement('div');
	divContent.style.border = '1px solid gainsboro';
	div.appendChild(divContent);
	divContent.style.overflow = 'hidden';
	divContent.style.marginLeft = '15px';
	
	var img  = document.createElement('img');
	img.src = 'images/plus.png';
	img.width = 13;
	img.style.float = 'left';
	img.style.margin = '2px 6px 2px 2px';
	
	this.ExpandCollapse = function(collapse)
	{
		if ((collapse && !collapsed) || (!collapse && collapsed))
			this.Toggle();
	};
	
	this.DrawHeader = function()
	{
		$(divHeader).empty();
		divHeader.appendChild(img);
		divHeader.appendChild(doc.GetHeader());
	};
	
	var InternalDraw = function(content)
	{
		if (!collapsed)
			divContent.style.height = divContent.offsetHeight;
		$(divContent).empty();
		divContent.appendChild(content);
		if (!collapsed)
			divContent.style.height = '';
	};
	
	this.DrawContent = InternalDraw;
	
	var RefreshInternal = function(done)
	{
		doc.GetContent(function(content)
		{
			if (content)
			{
				contentLoaded = true;
				InternalDraw(content);
			}
			if (done)
				done();
		});
	};
	
	this.Refresh = RefreshInternal;
	
	this.Toggle = function()
	{
		ToggleInternal();
	};
	
	var ToggleInternal = function()
	{
		if (collapsed)
		{
			divContent.style.height = null;
			divContent.style.minHeight = '10px';
			collapsed = false;
			img.src = 'images/minus.png';
		}
		else
		{
			divContent.style.height = '0px';
			divContent.style.minHeight = '0px';
			collapsed = true;
			img.src = 'images/plus.png';
		}
		if (!collapsed && !contentLoaded)
		{
			RefreshInternal();
		}
	};
	
	divHeader.onclick = ToggleInternal;
	divHeader.onkeydown = function(e)
	{
		if (e.keyCode == 32)
			ToggleInternal();
	};
}
