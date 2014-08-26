<?php
namespace Developer\PersistenceLayer;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface EntityProducerInterface
{
	public function createEntity();

	/**
	 * @return HydratorInterface
	 */
	public function getHydrator();
} 