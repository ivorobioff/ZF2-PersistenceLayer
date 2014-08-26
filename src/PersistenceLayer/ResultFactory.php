<?php
namespace Developer\PersistenceLayer;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class ResultFactory 
{
	private $entityProducer;

	public function __construct(EntityProducerInterface $entityProducer)
	{
		$this->entityProducer = $entityProducer;
	}

	/**
	 * @param $result
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public function prepareResultArray($result)
	{
		if (!is_array($result) && !$result instanceof \Iterator)
		{
			throw new \InvalidArgumentException('Result must be array or instance of Iterator');
		}

		if (is_array($result))
		{
			return $result;
		}

		return iterator_to_array($this->prepareResultIterator($result));
	}

	/**
	 * @param $result
	 * @return ResultIterator
	 * @throws \InvalidArgumentException
	 */
	public function prepareResultIterator($result)
	{
		if (!is_array($result) && !$result instanceof \Iterator)
		{
			throw new \InvalidArgumentException('Result must be array or instance of Iterator');
		}

		if (is_array($result))
		{
			$result = new \ArrayIterator($result);
		}

		return new ResultIterator($result, $this->entityProducer);
	}

	/**
	 * @param array $result
	 * @return EntityInterface
	 */
	public function prepareRow(array $result)
	{
		return static::produceEntityFromArray($result, $this->entityProducer);
	}

	static public function produceEntityFromArray(array $data, EntityProducerInterface $entityProducer)
	{
		$entity = $entityProducer->createEntity();
		$object = $entity;

		if ($entity instanceof StorageProviderEntityInterface)
		{
			$object = $entity->getStorage();
		}

		$hydrator = $entityProducer->getHydrator();
		$hydrator->hydrate($data, $object);

		return $entity;
	}
} 