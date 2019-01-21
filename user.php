<?php

class User
{
	private $id = 0;
	private $roles = [];
	private $department = Department::none;
	private $fio = '';
	
	public function __construct($id = 0, $group = Group::none, $department = Department::none, $fio = '')
	{
		$this->id = $id;
		$this->group = $group;
		$this->department = $department;
		$this->fio = $fio;
	}
	
	public function GetID()
	{
		return $this->id;
	}
}

?>