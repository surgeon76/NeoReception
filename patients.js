Patients.prototype = new Document;
Patients.prototype.constructor = Patients;
function Patients(pDoc)
{
	Document.call(this, {ID: -1}, pDoc, 'Patients');
	
	var records = null;
	
	this.GetHeader = function()
	{
		var div = document.createElement('div');
		div.innerText = "Электронная очередь";
		div.style.backgroundColor = '#7af7d3';
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
			
			records = new List(div, Registry, pDoc);
			records.DrawHeader();
			records.DrawContent();
			
			ready(div);
		});
	};
}
