<?php
namespace Developer\PersistenceLayer;
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
		$repositoryClass = $repositoryConfig['class'];

		return new  $repositoryClass(
			$config['default_pk_name'],
			$repositoryConfig['table'],
			$this->getServiceLocator()->get($config['adapter'])
		);
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