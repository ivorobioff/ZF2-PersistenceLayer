<?php
namespace Developer\PersistenceLayer\Cache\Strategy;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class StaticStrategy implements StrategyInterface
{
	protected $storage = [];

	public function has($method, array $args)
	{
		return isset($this->storage[$method][$this->prepareArgHash($args)]);
	}

	public function get($method, array $args)
	{
		return $this->storage[$method][$this->prepareArgHash($args)];
	}

	public function add($method, array $args, $value)
	{
		$this->storage[$method] = [$this->prepareArgHash($args) => $value];
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
			unset($this->storage);
		}
	}

	private function prepareArgHash(array $args)
	{
		return md5(serialize($args));
	}
}