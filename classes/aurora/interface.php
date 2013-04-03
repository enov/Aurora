<?php

interface Aurora_Interface
{
	public static function db_from_model($model);
	public static function db_to_model($model, array $row);
}