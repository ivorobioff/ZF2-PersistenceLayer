<?php
namespace Developer\PersistenceLayer\MongoDB;

use Developer\PersistenceLayer\EntityInterface;
use Developer\PersistenceLayer\EntityProducerInterface;
use Developer\PersistenceLayer\MapperInterface;
use Developer\PersistenceLayer\StorageProviderEntityInterface;
use Developer\PersistenceLayer\ResultFactory;
use Developer\Stuff\Exceptions\NotImplementedException;
use Zend\Stdlib\Hydrator\ObjectProperty;


/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
abstract class AbstractMapper implements MapperInterface, EntityProducerInterface
{
	/**
	 * @var \MongoCollection
	 */
	private $collection;

	/**
	 * @var ResultFactory
	 */
	private $resultFactory;

	public function __construct(\MongoCollection $collection)
	{
		$this->collection = $collection;
		$this->resultFactory = new ResultFactory($this);
	}

	public function getHydrator()
	{
		return new ObjectProperty();
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

		$object = $entity;

		if ($entity instanceof StorageProviderEntityInterface)
		{
			$object = $entity->getStorage();
		}

		$hydrator = $this->getHydrator();
		$data = $hydrator->extract($object);

		if (isset($data['_id']))
		{
			$_id = $data['_id'];
			unset($data['_id']);
			$result = $this->getCollection()->update(['_id' => $_id], $data);
		}
		else
		{
			$result = $this->getCollection()->insert($data);
			$hydrator->hydrate(['_id' => $data['_id']], $object);
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
		$result = $this->getCollection()->find();
		$result->sort(['_id' => 1]);
		return $this->prepareResult($result);
	}

	public function count()
	{
		throw new NotImplementedException(__METHOD__);
	}

	public function delete(EntityInterface $entity)
	{
		throw new NotImplementedException(__METHOD__);
	}

	protected function prepareResult($result)
	{
		return $this->resultFactory->prepareResultIterator($result);
	}

	protected function prepareRow(array $result = null)
	{
		if ($result === null) return null;
		return $this->resultFactory->prepareRow($result);
	}
}