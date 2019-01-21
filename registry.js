Registry.prototype = new Document;
Registry.prototype.constructor = Registry;
function Registry(d)
{
	Document.call(this, {ID: SpecialID.NotSerializable}, null, 'Registry');
	
	var account = new Account(jQuery.extend({}, d), this);
	var passport = new Passport(jQuery.extend({}, d), this);
	var reception = new Reception(jQuery.extend({}, d), this);
	reception.SetPassport(passport);
	account.SetReception(reception);
	
	this.GetHeader = function()
	{
		var div = document.createElement('div');

		var isPatient = Registry.currentUser.GetPermissions() == Permissions.patient;
		var status = reception.GetData().Status || Status.NoAction;
		if (isPatient)
		{
			div.innerText = "Окно электронной регистратуры";
		}
		else
		{
			div.style.fontSize = 14;
			var statusText = GetStatusText(status);
			div.innerHTML = '<table class="tableListHeader"><tr><td style="width: 400px;">' + $("<div>").text(passport.GetData().FIO).html() + '</td>' +
				'<td style="width: 200px;"><b>' + statusText + '</b></td>' +
				'<td>' + moment(reception.GetData().DateMade).format("YYYY-MM-DD HH:mm") + '</td></tr></table>';
		}
		div.style.backgroundColor = isPatient ? '#7af7d3' : GetStatusColor(status);
		
		return div;
	};
	
	this.GetContent = function(ready)
	{
		this.Load(function(success)
		{
			if (!success)
			{
				ready(null);
				return;
			}
			
			var div = document.createElement('div');

			if (Registry.currentUser.GetPermissions() == Permissions.patient)
			{
				var blockAccount = new Block(div, account);
				blockAccount.DrawHeader();
			}
			
			var blockPass = new Block(div, passport);
			blockPass.DrawHeader();
			blockPass.Toggle();
			
			var blockReception = new Block(div, reception);
			blockReception.DrawHeader();
			blockReception.Toggle();

			ready(div);
		});
	};
}
Registry.currentUser = new CurrentUser();
