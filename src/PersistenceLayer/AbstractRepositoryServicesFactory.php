<?php
namespace Developer\PersistenceLayer;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class AbstractRepositoryServicesFactory implements AbstractFactoryInterface
{
	private $mappersConfig;

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
		$config = $this->findRepositoryServiceConfig($requestedName, $serviceLocator->get('Config')['repository']);
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
		$serviceConfig = $this->findRepositoryServiceConfig($requestedName, $config);
		$repositoryConfig = $config[$serviceConfig['repository_alias']];

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

	private function findRepositoryServiceConfig($serviceName, array $config)
	{
		if (is_null($this->mappersConfig))
		{
			$mappers = [];

			foreach ($config as $repositoryAlias => $repositoryConfig)
			{
				foreach ($repositoryConfig['services'] as $serviceAlias => $serviceConfig)
				{
					$serviceConfig['repository_alias'] = $repositoryAlias;
					$mappers[$serviceAlias] = $serviceConfig;
				}
			}

			$this->mappersConfig = $mappers;
		}

		if (!isset($this->mappersConfig[$serviceName])) return null;

		return $this->mappersConfig[$serviceName];
	}
}