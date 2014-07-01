<?php
namespace Developer\PersistenceLayer\MongoDB;
use Developer\PersistenceLayer\RepositoryFactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class MapperFactory implements RepositoryFactoryInterface, ServiceLocatorAwareInterface
{
	private $serviceLocator;

	public function createRepository(
		$repositoryName,
		$repositoryConfig,
		$config
	)
	{
		/**
		 * @var \MongoClient $client
		 */
		$client = $this->getServiceLocator()->get($config['client']);
		$collection = $client->selectCollection($config['database'], $repositoryConfig['table']);
		$repository = new $repositoryConfig['class']($collection);
		return $repository;
	}

	/**
	 * Set service locator
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 */
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
		$this->serviceLocator = $serviceLocator;
	}

	/**
	 * Get service locator
	 *
	 * @return ServiceLocatorInterface
	 */
	public function getServiceLocator()
	{
		return $this->serviceLocator;
	}
}