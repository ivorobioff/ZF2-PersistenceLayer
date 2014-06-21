<?php
namespace Developer\PersistenceLayer;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
abstract class AbstractEntity
{
	public function __isset($name)
	{
		$m = 'get'.ucfirst($name);
		return method_exists($this, $m)
			&& call_user_func($this, $m) !== null;
	}

	public function __get($name)
	{
		return call_user_func([$this, 'get'.ucfirst($name)]);
	}

	public function __set($name, $value)
	{
		call_user_func([$this, 'set'.ucfirst($name)], $value);
	}

}