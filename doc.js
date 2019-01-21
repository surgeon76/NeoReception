var SpecialID = 
{
	NotLoadable: 0,
	NotSerializable: -1,
	NotSaveable: -2
};

function Document(data, parentDoc, objName)
{
	var view = null;
	var controls = new Controls();
	var loaded = false;
	var loading = false;
	var queryAdd = '';
	var querySaveAdd = '';
	
	this.SetQueryAdd = function(add)
	{
		queryAdd = add;
	};
	
	this.SetQuerySaveAdd = function(add)
	{
		querySaveAdd = add;
	};
	
	var NeedLoad = function()
	{
		return (data == null || data.ID == null || data.ID === SpecialID.NotLoadable || data.ID === SpecialID.NotSerializable || loaded) ? false : true;
	};
	
	this.Load = function(done)
	{
		if (loading)
			return false;
		
		if (!NeedLoad())
		{
			if (done)
				done(true);
			return true;
		}
	
		loading = true;
		var query = "object&name=" + objName + "&action=load&id=" + data.ID + queryAdd;
		queryAdd = '';
		return Query(query, function(success, d, textStatus, jqXHR)
		{
			loading = false;
			if (success && d.length > 0)
			{
				data = d[0];
				loaded = true;
			}
			if (done)
				done(success);
		});
	};
	
	var SaveChain = function(done)
	{
		if (parentDoc)
		{
			parentDoc.Save(function(success)
			{
				if (!success)
				{
					done(false);
					return;
				}
				
				SaveInternal(function(success)
				{
					done(success);
				});
			});
		}
		else
		{
			SaveInternal(function(success)
			{
				done(success);
			});
		}
	};
	
	this.Save = function(done)
	{
		SaveChain(function(success)
		{
			done(success);
		});
	};
	
	var SaveClickInternal = function(done)
	{
		SaveChain(function(success)
		{
			done(success);
		});
	};
	
	this.SaveClick = function()
	{
		SaveClickInternal(function(success)
		{
		});
	};
	
	var SaveInternal = function(done)
	{
		if (data.ID < 0 || !loaded)
		{
			done(true);
			return;
		}
			
		for (var name in controls.GetCollection())
		{
			data[name] = controls.GetValue(name);
		}
		
		var id = data.ID;
		var parentIDreq = '';
		if (id == null || id === SpecialID.NotLoadable)
		{
			id = 0;
			parentIDreq = '&parentID=' + parentDoc.GetData().ID;
		}
		var query = "object&name=" + objName + "&action=save&id=" + id + parentIDreq + querySaveAdd;
		querySaveAdd = '';
		Query(query, function(success, d, textStatus, jqXHR)
		{
			if (success)
			{
				if (id == 0)
					data.ID = d[0].ID;
			}
			done(success);
		},
		data);
	};
	
	this.GetData = function()
	{
		return data;
	};
	
	this.SetData = function(d)
	{
		data = d;
	};
	
	this.GetParentDoc = function()
	{
		return parentDoc;
	};

	this.SetView = function(v)
	{
		view = v;
	};
	
	this.GetView = function()
	{
		return view;
	};
	
	this.SetLoaded = function(load)
	{
		loaded = load;
	};
	
	this.GetControls = function()
	{
		return controls;
	};
}
