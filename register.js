function Register(obj)
{
	Register.counter++;
	Register.hash[Register.counter] = obj;
	return Register.counter;
}
Register.hash = {};
Register.counter = 0;
