<?php
namespace Developer\PersistenceLayer\MongoDB;

use Developer\PersistenceLayer\EntityInterface;
use Developer\PersistenceLayer\EntityProducerInterface;
use Developer\PersistenceLayer\MapperInterface;
use Developer\PersistenceLayer\ResultIterator;
use Developer\Stuff\Exceptions\NotImplementedException;
use Developer\Stuff\Hydrators\ValuesBinder;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
abstract class AbstractMapper implements MapperInterface, EntityProducerInterface
{
	/**
	 * @var \MongoCollection
	 */
	private $collection;

	public function __construct(\MongoCollection $collection)
	{
		$this->collection = $collection;
	}

	/**
	 * @return \MongoCollection
	 */
	protected function getCollection()
	{
		return $this->collection;
	}

	public function save(EntityInterface $entity)
	{
		$this->getCollection()->save($entity);
	}

	public function load($primKey)
	{
		throw new NotImplementedException(__METHOD__);
	}

	public function loadAll()
	{
		throw new NotImplementedException(__METHOD__);
	}

	public function count()
	{
		throw new NotImplementedException(__METHOD__);
	}

	public function delete($primKey)
	{
		throw new NotImplementedException(__METHOD__);
	}

	/**
	 * @param $result
	 * @return EntityInterface|null
	 */
	protected function prepareRow($result)
	{
		if ($result === null) return null;

		$entity = $this->createEntity();
		(new ValuesBinder())->hydrate($result, $entity);

		return $entity;
	}

	/**
	 * @param $result
	 * @return ResultIterator|null
	 */
	protected function prepareResult($result)
	{
		if ($result === null) return null;
		return new ResultIterator($result, $this);
	}
}