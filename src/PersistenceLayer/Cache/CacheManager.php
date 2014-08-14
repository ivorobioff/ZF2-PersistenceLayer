<?php
namespace Developer\PersistenceLayer\Cache;
use Developer\PersistenceLayer\MapperInterface;
use Developer\PersistenceLayer\RepositoryAwareInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class CacheManager implements CacheManagerInterface, RepositoryAwareInterface
{
	private $cache = [];

	/**
	 * @var MapperInterface
	 */
	private $repository;

	public function call($method)
	{
		$args = $this->prepareArgs(func_get_args());

		if (!$this->hasCache($method, $args))
		{
			$value = call_user_func_array([$this->getRepository(), $method], $args);
			$this->addCache($method, $args, $value);
		}

		return $this->getCache($method, $args);
	}

	public function clear($method)
	{
		$args = $this->prepareArgs(func_get_args());
		unset($this->cache[$method][$this->prepareArgHash($args)]);
	}

	public function clearAll($method = null)
	{
		if ($method)
		{
			unset($this->cache[$method]);
		}
		else
		{
			unset($this->cache);
		}
	}

	private function prepareArgs(array $args)
	{
		unset($args[0]);
		return array_values($args);
	}

	private function prepareArgHash(array $args)
	{
		return md5(serialize($args));
	}

	private function addCache($method, array $args, $value)
	{
		$this->cache[$method][$this->prepareArgHash($args)] = $value;
	}

	private function hasCache($method, array $args)
	{
		return isset($this->cache[$method][$this->prepareArgHash($args)]);
	}

	private function getCache($method, array $args)
	{
		return $this->cache[$method][$this->prepareArgHash($args)];
	}

	public function setRepository(MapperInterface $mapper)
	{
		$this->repository = $mapper;
	}

	/**
	 * @return MapperInterface
	 */
	public function getRepository()
	{
		return $this->repository;
	}
}