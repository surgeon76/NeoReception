<?php

abstract class DataBase
{
	const querySelect = 0;
	const queryUpdate = 1;
	const queryInsert = 2;
	const queryDelete = 3;
	
	protected $db = null;
	
	public function Connect($cstring)
	{
	}
	
	public function Close()
	{
	}
	
	public function GetVal($sql)
	{
	}
	
	public function Query($sql, $type = DataBase::querySelect)
	{
	}
	
	public function BeginTrans()
	{
	}
	
	public function CommitTrans()
	{
	}
	
	public function Rollback()
	{
	}
	
	public function PrepareString($s)
	{
	}
	
	public function PrepareDate($d)
	{
	}
}

class DataBasePG extends DataBase
{
	public function __construct($cstring = null)
	{
		if (!is_null($cstring))
			$this->Connect($cstring);
	}

	public function Connect($cstring)
	{
		try
		{
			$this->db = pg_pconnect($cstring);
			if (!$this->db)
				throw (new Exception('postgres DB connect failed'));
		}
		catch (Exception $e)
		{
			$this->db = null;
			WriteLog($e->getMessage());
			die();
		}
	}
	
	public function Close()
	{
		try
		{
			if (!pg_close($this->db))
				throw new Exception('postgres DB close failed');
		}
		catch (Exception $e)
		{
			WriteLog($e->getMessage());
		}
	}
	
	public function GetVal($sql)
	{
		$ret = null;
		
		try
		{
			$val = pg_query($this->db, $sql);
			if (!$val)
				throw new Exception('postgres DB get value failed');
			
			$ret = null;
			$row = pg_fetch_row($val);
			if ($row)
				$ret = $row[0];
		}
		catch (Exception $e)
		{
			WriteLog($e->getMessage());
		}
		
		return $ret;
	}
	
	public function Query($sql, $type = DataBase::querySelect)
	{
		$ret = null;
				
		try
		{
			$val = pg_query($this->db, $sql);
			if (!$val)
				throw new Exception('postgres DB query failed');
			
			$ret = pg_fetch_all($val);
			if (!CheckArray($ret))
				$ret = [];
		}
		catch (Exception $e)
		{
			WriteLog($e->getMessage());
		}
		
		return $ret;
	}
	
	public function BeginTrans()
	{
		try
		{
			$val = pg_query($this->db, 'begin');
			if (!$val)
				throw new Exception('postgres DB begin transaction failed');
		}
		catch (Exception $e)
		{
			WriteLog($e->getMessage());
		}
	}
	
	public function CommitTrans()
	{
		try
		{
			$val = pg_query($this->db, 'commit');
			if (!$val)
				throw new Exception('postgres DB commit transaction failed');
		}
		catch (Exception $e)
		{
			WriteLog($e->getMessage());
		}
	}
	
	public function Rollback()
	{
		try
		{
			$val = pg_query($this->db, 'rollback');
			if (!$val)
				throw new Exception('postgres DB rollback transaction failed');
		}
		catch (Exception $e)
		{
			WriteLog($e->getMessage());
		}
	}
	
	public function PrepareString($s)
	{
		return pg_escape_string($this->db, $s);
	}
	
	public function PrepareDate($d)
	{
		return $d;
	}
}

class DataBaseMySQL extends DataBase
{
	public function __construct($cstring = null)
	{
		if (!is_null($cstring))
			$this->Connect($cstring);
	}

	public function Connect($cstring)
	{
		try
		{
			$arr = explode('|', $cstring);
			$this->db = mysqli_connect($arr[0], $arr[1], $arr[2], $arr[3]);
			if (!$this->db)
				throw (new Exception('MySQL DB connect failed'));
			mysqli_query($this->db, "set names utf8");
		}
		catch (Exception $e)
		{
			$this->db = null;
			WriteLog($e->getMessage());
			die();
		}
	}
	
	public function Close()
	{
		try
		{
			if (!mysqli_close($this->db))
				throw new Exception('MySQL DB close failed');
		}
		catch (Exception $e)
		{
			WriteLog($e->getMessage());
		}
	}
	
	public function GetVal($sql)
	{
		$ret = null;
		
		try
		{
			$val = mysqli_query($this->db, $sql);
			if (!$val || gettype($val) != "object")
				throw new Exception('MySQL DB get value failed');
			
			if (mysqli_num_rows($val) >= 1)
			{
				$row = mysqli_fetch_array($val);
				if ($row)
					$ret = $row[0];
			}
			
			if ($val !== true)
				mysqli_free_result($val);
		}
		catch (Exception $e)
		{
			WriteLog($e->getMessage());
		}
		
		return $ret;
	}
	
	public function Query($sql, $type = DataBase::querySelect)
	{
		$ret = null;
				
		try
		{
			$val = mysqli_query($this->db, $sql);
			if (!$val)
				throw new Exception('MySQL DB query failed');
			
			if (gettype($val) == "object")
			{
				$ret = [];
				while ($row = mysqli_fetch_assoc($val))
				{
					$ret[] = $row;
				}
				//$ret = mysqli_fetch_all($val, MYSQLI_ASSOC);
				//if (!CheckArray($ret))
				//	$ret = [];
			}
			
			if ($val !== true)
				mysqli_free_result($val);
			else
				$ret = true;
		}
		catch (Exception $e)
		{
			WriteLog($e->getMessage());
		}
		
		return $ret;
	}
	
	public function BeginTrans()
	{
		try
		{
			$val = mysqli_begin_transaction($this->db);
			if (!$val)
				throw new Exception('MySQL DB begin transaction failed');
		}
		catch (Exception $e)
		{
			WriteLog($e->getMessage());
		}
	}
	
	public function CommitTrans()
	{
		try
		{
			$val = mysqli_commit($this->db, 'commit');
			if (!$val)
				throw new Exception('MySQL DB commit transaction failed');
		}
		catch (Exception $e)
		{
			WriteLog($e->getMessage());
		}
	}
	
	public function Rollback()
	{
		try
		{
			$val = mysqli_rollback($this->db);
			if (!$val)
				throw new Exception('MySQL DB rollback transaction failed');
		}
		catch (Exception $e)
		{
			WriteLog($e->getMessage());
		}
	}
	
	public function PrepareString($s)
	{
		return mysqli_real_escape_string($this->db, $s);
	}
	
	public function PrepareDate($d)
	{
		return $d;
	}
}

?>