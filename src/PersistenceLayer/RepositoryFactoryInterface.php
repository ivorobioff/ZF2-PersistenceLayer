<?php
namespace Developer\PersistenceLayer;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface RepositoryFactoryInterface
{
	public function createRepository(
		$repositoryName,
		$repositoryConfig,
		$config
	);
} 