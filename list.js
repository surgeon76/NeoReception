ListSortCommand.sortAsc = 0;
ListSortCommand.sortDesc = 1;

function ListSortCommand(n, c)
{
	this.name = n;
	this.command = c;
}

function List(parentEl, itemClass, parentDoc, sortCommandIndex)
{
	var key = Register(this);
	
	var data = null;
	
	var items = [];
	var sortCommands = [];
	
	sortCommandIndex = sortCommandIndex || -1;
	
	var sortCombo = null;
	
	var loaded = false;
	var loading = false;
	
	var div = document.createElement('div');
	parentEl.appendChild(div);
	div.view = this;
	
	var divHeader = document.createElement('div');
	div.appendChild(divHeader);
	
	var table = document.createElement('table');
	table.className = "tableListHeader";
	table.style.width = "100%";
	div.appendChild(table);
	
	if (itemClass.GetSortCommands)
		sortCommands = itemClass.GetSortCommands();
	
	var Sort = function(index)
	{
		sortCommandIndex = index;
		
		loaded = false;
		Draw();
	};
	
	var GetSortQuery = function()
	{
		if (sortCommandIndex >= 0)
			return '&sortCommand=' + sortCommands[sortCommandIndex].command;
		
		return '';
	};
	
	var DrawSortHeader = function(parentEl)
	{
		if (sortCommands.length == 0)
			return;
		
		var sortlabel = document.createElement('label');
		sortlabel.innerHTML = "Сортировка: ";
		parentEl.appendChild(sortlabel);
		
		var sortItems = [];
		for (var i = 0; i < sortCommands.length; i++)
		{
			sortItems.push(new ComboBoxItem(sortCommands[i].name, i));
		}
		sortCombo = new ComboBoxLight(parentEl, sortItems, Sort);
		sortCombo.SelectValue(sortCommandIndex);
	};
	
	this.SetSortCommands = function(cmd)
	{
		sortCommands = cmd;
	};
	
	this.SetLoaded = function(load)
	{
		loaded  = load;
	};
	
	var LoadInternal = function(done)
	{
		if (loading)
			return false;
		
		if (loaded)
		{
			done(true);
			return true;
		}
		
		loading = true;
		var query = "list&name=" + itemClass.prototype.constructor.name + (parentDoc == null ? '' : "&id=" + parentDoc.GetData().ID) + GetSortQuery();
		return Query(query, function(success, d, textStatus, jqXHR)
		{
			loading = false;
			if (success)
			{
				data = d;
				loaded = true;
			}
			done(success);
		});
	};
	
	this.Load = function(done)
	{
		return LoadInternal(done);
	};
	
	var AddItem = function(item, atBegin)
	{
		var tr = document.createElement('tr');
		if (atBegin)
			table.insertBefore(tr, table.firstChild ? table.firstChild.nextSibling : null);
		else
			table.appendChild(tr);
		var td = document.createElement('td');
		tr.appendChild(td);
			
		var block = new Block(td, item);
		items.push(item);
		block.DrawHeader();
	};
	
	this.AddNew = function()
	{
		var item = new itemClass(null, parentDoc);
		AddItem(item, true);
		item.GetView().Toggle();
	};
	
	this.DrawHeader = function()
	{
		$(divHeader).empty();
		
		DrawSortHeader(divHeader);
		
		var add = document.createElement('input');
		add.setAttribute('type', 'button');
		add.setAttribute('value', 'New');
		add.onclick = this.AddNew;
		//divHeader.appendChild(add);
	};
	
	var Draw = function()
	{
		LoadInternal(function()
		{
			$(table).empty();
			items = [];

			for (var i in data)
			{
				AddItem(new itemClass(data[i], parentDoc));
			}
		});
	};
	
	this.DrawContent = function()
	{
		Draw();
	};
}
