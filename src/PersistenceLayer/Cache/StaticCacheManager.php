<?php
namespace Developer\PersistenceLayer\Cache;

use Developer\PersistenceLayer\MapperInterface;
use Developer\PersistenceLayer\RepositoryAwareInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class StaticCacheManager implements  RepositoryAwareInterface
{
	/**
	 * @var Storage
	 */
	protected $storage;
	/**
	 * @var MapperInterface
	 */
	private $repository;

	public function __construct()
	{
		$this->storage = new Storage();
	}

	public function call($method)
	{
		$args = $this->prepareArgs(func_get_args());

		if (!$this->storage->has($method, $args))
		{
			$value = call_user_func_array([$this->getRepository(), $method], $args);
			$this->storage->add($method, $args, $value);
		}

		return $this->storage->get($method, $args);
	}

	public function clear($method)
	{
		$args = $this->prepareArgs(func_get_args());
		$this->storage->clear($method, $args);
	}

	public function clearAll($method = null)
	{
		$this->storage->clearAll($method);
	}

	private function prepareArgs(array $args)
	{
		unset($args[0]);
		$args = array_values($args);

		return $args;
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