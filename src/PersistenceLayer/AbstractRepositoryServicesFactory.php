<?php
namespace Developer\PersistenceLayer;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class AbstractRepositoryServicesFactory implements AbstractFactoryInterface
{
	private $servicesMap;

	/**
	 * Determine if we can create a service with name
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @param $name
	 * @param $requestedName
	 * @return bool
	 */
	public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
	{
		$config = $this->findRepositoryAliasByServiceName($requestedName, $serviceLocator->get('Config')['repository']);
		return !is_null($config);
	}

	/**
	 * Create service with name
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @param $name
	 * @param $requestedName
	 * @return mixed
	 */
	public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
	{
		$config = $serviceLocator->get('Config')['repository'];
		$repositoryAlias = $this->findRepositoryAliasByServiceName($requestedName, $config);
		$repositoryConfig = $config[$repositoryAlias];

		/**
		 * @var AbstractRepositoryFactory $factory
		 */
		$factory = new $repositoryConfig['factory'](
			$requestedName,
			$repositoryConfig['services'][$requestedName],
			$repositoryConfig,
			$serviceLocator
		);

		return $factory->createRepository();
	}

	private function findRepositoryAliasByServiceName($serviceName, array $config)
	{
		if (is_null($this->servicesMap))
		{
			$servicesMap = [];

			foreach ($config as $repositoryAlias => $repositoryConfig)
			{
				foreach (array_keys($repositoryConfig['services']) as $serviceAlias)
				{
					$servicesMap[$serviceAlias] = $repositoryAlias;
				}
			}

			$this->servicesMap = $servicesMap;
		}

		if (!isset($this->servicesMap[$serviceName])) return null;

		return $this->servicesMap[$serviceName];
	}
}