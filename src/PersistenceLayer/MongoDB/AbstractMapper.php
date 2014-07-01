<?php
namespace Developer\PersistenceLayer\MongoDB;

use Developer\PersistenceLayer\EntityInterface;
use Developer\PersistenceLayer\MapperInterface;
use Developer\Stuff\Exceptions\NotImplementedException;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
abstract class AbstractMapper implements MapperInterface
{
	/**
	 * @var \MongoCollection
	 */
	private $collection;

	public function __construct(\MongoCollection $collection)
	{
		$this->collection = $collection;
	}

	abstract public function createEntity();

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
}