<?php

function CheckString($s)
{
	return (string)$s === $s;
}

function CheckArray($arr)
{
	return (array)$arr === $arr;
}

abstract class SQLSyntax
{
    const Postgres = 0;
    const MySQL = 1;
}

function GetSQLSyntax($obj)
{
	$name = get_class($obj);
	if ($name == "DataBaseMySQL")
		return SQLSyntax::MySQL;
	else
		return SQLSyntax::Postgres;
}

?>