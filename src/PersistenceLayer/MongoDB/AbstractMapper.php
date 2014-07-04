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

	/**
	 * @param EntityInterface|AbstractEntity $entity
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 */
	public function save(EntityInterface $entity)
	{
		if (!$entity instanceof AbstractEntity)
		{
			throw new \InvalidArgumentException('Entity must be instance of AbstractEntity');
		}

		$data = (new ValuesBinder())->extract($entity);

		if (isset($data['_id']))
		{
			unset($data['_id']);
			$result = $this->getCollection()->update(['_id' => $entity->_id], $data);
		}
		else
		{
			$result = $this->getCollection()->insert($data);
			$entity->_id = $data['_id'];
		}

		if ($result['ok'] != 1)
		{
			throw new \RuntimeException('MongoDB: '.$result['err']);
		}
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