<?php

use Illuminate\Container\Container;

function app($make = null, array $parameters = [])
{
	if (is_null($make)) {
		return Container::getInstance();
	}

	return Container::getInstance()->make($make, $parameters);
}

function config($key = null, $default = null)
{

	if (is_null($key)) {
		return app('config');
	}

	if (is_array($key)) {
		return app('config')->set($key);
	}

	return app('config')->get($key, $default);
}

function database_path($path = '')
{
	return app()->databasePath($path);
}