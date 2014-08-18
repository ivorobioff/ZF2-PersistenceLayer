<?php
namespace Developer\PersistenceLayer\Cache;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class Storage 
{
	protected $storage = [];

	public function has($method, array $args)
	{
		if (!isset($this->storage[$method]))
		{
			return false;
		}

		return array_key_exists($this->prepareArgHash($args), $this->storage[$method]);
	}

	public function get($method, array $args)
	{
		return $this->storage[$method][$this->prepareArgHash($args)];
	}

	public function add($method, array $args, $value)
	{
		$this->storage[$method][$this->prepareArgHash($args)] = $value;
	}

	public function clear($method, array $args)
	{
		unset($this->storage[$method][$this->prepareArgHash($args)]);
	}

	public function clearAll($method = null)
	{
		if ($method)
		{
			unset($this->storage[$method]);
		}
		else
		{
			$this->storage = [];
		}
	}

	private function prepareArgHash(array $args)
	{
		return md5(serialize($args));
	}
} 