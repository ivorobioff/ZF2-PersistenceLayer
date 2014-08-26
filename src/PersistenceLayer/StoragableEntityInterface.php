<?php
namespace Developer\PersistenceLayer;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface StoragableEntityInterface extends EntityInterface
{
	/**
	 * @return object
	 */
	public function getStorage();
} 