<?php
namespace Developer\PersistenceLayer;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface StorageProviderEntityInterface extends EntityInterface
{
	/**
	 * @return object
	 */
	public function getStorage();
} 