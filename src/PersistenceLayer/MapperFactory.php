<?php
namespace Developer\PersistenceLayer;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class MapperFactory extends AbstractRepositoryFactory
{
	public function createRepository()
	{
		$repositoryClass = $this->repositoryConfig['class'];

		return new  $repositoryClass(
			$this->config['default_pk_name'],
			$this->repositoryConfig['table'],
			$this->serviceLocator->get($this->config['adapter'])
		);
	}
}