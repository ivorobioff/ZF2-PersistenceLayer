<?php
namespace Developer\PersistenceLayer;

use Developer\PersistenceLayer\Cache\StaticCacheManager;
use Developer\PersistenceLayer\Cache\StaticCacheManagerAwareInterface;
use Developer\PersistenceLayer\Plugins\PluginsProviderInterface;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class AbstractRepositoryFactory implements AbstractFactoryInterface
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
		$commonConfig = $config[$repositoryAlias];

		/**
		 * @var RepositoryFactoryInterface|ServiceLocatorAwareInterface $factory
		 */

		$factory = new $commonConfig['factory']();

		if ($factory instanceof ServiceLocatorAwareInterface)
		{
			$factory->setServiceLocator($serviceLocator);
		}

		$repositoryConfig = $commonConfig['services'][$requestedName];

		$repository = $factory->createRepository(
			$requestedName,
			$repositoryConfig,
			$commonConfig
		);

		if ($repository instanceof PluginsProviderInterface)
		{
			$pluginsConfig = isset($repositoryConfig['plugins']) ? $repositoryConfig['plugins'] : [];
			$repository->setPluginsConfig($pluginsConfig);
		}

		if ($repository instanceof StaticCacheManagerAwareInterface)
		{
			$staticCacheManager = new StaticCacheManager();
			$staticCacheManager->setRepository($repository);
			$repository->setStaticCacheManager($staticCacheManager);
		}

		return $repository;
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