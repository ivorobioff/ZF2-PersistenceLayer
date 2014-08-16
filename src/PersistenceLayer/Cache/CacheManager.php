<?php
namespace Developer\PersistenceLayer\Cache;

use Developer\PersistenceLayer\Cache\Strategy\StrategyInterface;
use Developer\PersistenceLayer\MapperInterface;
use Developer\PersistenceLayer\RepositoryAwareInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class CacheManager implements CacheManagerInterface, RepositoryAwareInterface
{
	private $strategy;

	/**
	 * @var MapperInterface
	 */
	private $repository;

	public function call($method)
	{
		$args = $this->prepareArgs(func_get_args());

		if (!$this->getStrategy()->has($method, $args))
		{
			$value = call_user_func_array([$this->getRepository(), $method], $args);
			$this->getStrategy()->add($method, $args, $value);
		}

		return $this->getStrategy()->get($method, $args);
	}

	public function clear($method)
	{
		$args = $this->prepareArgs(func_get_args());
		$this->getStrategy()->clear($method, $args);
	}

	public function clearAll($method = null)
	{
		$this->getStrategy()->clearAll($method);
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

	public function setStrategy(StrategyInterface $strategy)
	{
		$this->strategy = $strategy;
	}

	/**
	 * @return StrategyInterface
	 */
	public function getStrategy()
	{
		return $this->strategy;
	}
}