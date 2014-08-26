<?php
namespace Developer\PersistenceLayer;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class ResultIterator extends \IteratorIterator implements \Countable
{
	private $entityProducer;

	public function __construct(\Traversable $iterator, EntityProducerInterface $entityProducer)
	{
		parent::__construct($iterator);
		$this->entityProducer = $entityProducer;
	}

	public function current()
	{
		return ResultFactory::produceEntityFromArray(parent::current(), $this->entityProducer);
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