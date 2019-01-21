function Init(done, fail)
{
	$.when(Registry.currentUser.Load()).then(done, fail);
}

function Layout()
{
	//if (!CheckHTML5Support())
	//	return;
	
	Init(function()
	{
		var divlogout = document.createElement('div');
		divlogout.style.position = 'relative';
		divlogout.style.width = 800;
		document.body.appendChild(divlogout);
		
		var logout = document.createElement('a');
		logout.href = "logout.php";
		logout.innerHTML = "Выход";
		logout.style.fontSize = 12;
		logout.style.position = 'absolute';
		logout.style.right = 4;
		logout.style.top = 2;
		divlogout.appendChild(logout);
		
		var div = document.createElement('div');
		document.body.appendChild(div);
		$(div).css('width', '800px').css('border', '1px solid gainsboro');
		
		var info = document.createElement('a');
		info.href = "#";
		info.innerHTML = "Инструкции";
		info.style.fontSize = 12;
		info.style.position = 'absolute';
		info.style.right = 52;
		info.style.top = 2;
		info.onclick = function(e)
		{
			e.preventDefault();
		
			$("#infoDialog").dialog();
			$("#infoDialog").dialog("option", "width", 800);
		};
		divlogout.appendChild(info);

		if (Registry.currentUser.GetPermissions() == Permissions.patient)
		{
			var id = Registry.currentUser.GetData().ID;
			
			var query = "object&name=Account&action=expire&id=" + id;
			Query(query, function(success, d, textStatus, jqXHR)
			{
				var registry = new Registry(id > 0 ? {ID: id} : {ID: null});
				var blockRegistry = new Block(div, registry);
				blockRegistry.DrawHeader();
				blockRegistry.Toggle();
			});
		}
		else
		{
			var patients = new Patients(null);
			var blockPatients = new Block(div, patients);
			blockPatients.DrawHeader();
			blockPatients.Toggle();
			
			var vs = document.createElement("div");
			vs.style.height = 20;
			div.appendChild(vs);
			
			var log = new Log(null);
			var blockLog = new Block(div, log);
			blockLog.DrawHeader();
		}
		
		DoCheckLoggedIn();
	},
	function()
	{
		alert('Ошибка инициализации');
	});
}
