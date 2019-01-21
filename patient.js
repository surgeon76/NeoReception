Patient.prototype = new Document;
Patient.prototype.constructor = Patient;
function Patient(d)
{
	Document.call(this, {ID: 0}, null, 'Patient');
	
	var passport = new Passport(d, this);
	var card = new Card({ID: d.CardID}, passport);
	var cases = new Cases(passport);
	var exams = new Examinations(passport);
	var reception = new Reception(passport);
	
	this.GetHeader = function()
	{
		var table = document.createElement('table');
		
		var tr = document.createElement('tr');
		table.appendChild(tr);
		
		var columns = Patient.GetColumns();
		for (var i = 0; i < columns.length; i++)
		{
			var td = document.createElement('td');
			tr.appendChild(td);
			
			var text = '';
			for (var j = 0; j < columns[i].fields.length; j++)
			{
				text += passport.GetData()[columns[i].fields[j]] + ' ';
			}
			td.innerText = text;
			td.style.width = '' + columns[i].width + 'px';
		}
		table.border = 1;
		table.style.backgroundColor = 'Gainsboro';
		
		return table;
	};
	
	this.GetContent = function(ready)
	{
		var self = this;
		this.Load(function(success)
		{
			if (!success)
			{
				ready(null);
				return;
			}
			
			var div = document.createElement('div');

			var blockPass = new Block(div, passport);
			blockPass.DrawHeader();

			var blockCard = new Block(div, card);
			blockCard.DrawHeader();
			
			var blockCases = new Block(div, cases);
			blockCases.DrawHeader();
			blockCases.Toggle();

			var blockExams = new Block(div, exams);
			blockExams.DrawHeader();
			
			var blockReception = new Block(div, reception);
			blockReception.DrawHeader();

			ready(div);
		});
	};
}