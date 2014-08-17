<?php
namespace Developer\PersistenceLayer\Cache\Strategy;
use Zend\Session\Container;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class SessionStrategy implements StrategyInterface
{
	use ArgumentsHashCapableTrait;

	private $storage;

	public function __construct()
	{
		$this->storage = $this->createStorage();
	}

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
		$arr = [];

		if (isset($this->storage[$method]))
		{
			$arr = $this->storage[$method];
		}

		$arr[$this->prepareArgHash($args)] = $value;
		$this->storage[$method] = $arr;
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
			$this->storage = $this->createStorage();
		}
	}

	private function createStorage()
	{
		return new Container('developer_cache_manager');
	}
}