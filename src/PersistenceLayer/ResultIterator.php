<?php
namespace Developer\PersistenceLayer;
use Developer\Stuff\Hydrators\ValuesBinder;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class ResultIterator extends \IteratorIterator
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
		(new ValuesBinder())->hydrate(parent::current(), $entity);
		return $entity;
	}
} 