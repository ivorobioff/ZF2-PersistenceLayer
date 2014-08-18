<?php
namespace Developer\PersistenceLayer;

use Zend\Stdlib\Hydrator\ObjectProperty;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class ResultIterator extends \IteratorIterator implements \Countable
{
	private $repository;

	public function __construct(\Traversable $iterator, EntityProducerInterface $repository)
	{
		parent::__construct($iterator);
		$this->repository = $repository;
	}

	public function current()
	{
		$entity = $this->repository->createEntity();
		$hydrator = new ObjectProperty();
		$hydrator->hydrate(parent::current(), $entity);
		return $entity;
	}

	public function count()
	{
		$innerIterator = $this->getInnerIterator();

		if (!$innerIterator instanceof \Countable)
		{
			throw new \RuntimeException('Result iterator is not countable');
		}

		return $innerIterator->count();
	}
} 