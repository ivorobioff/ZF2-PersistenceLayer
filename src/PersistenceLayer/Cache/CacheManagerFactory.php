<?php
namespace Developer\PersistenceLayer\Cache;
use Developer\PersistenceLayer\RepositoryAwareInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class CacheManagerFactory implements FactoryInterface
{
	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return CacheManagerInterface|RepositoryAwareInterface
	 */
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$config = $serviceLocator->get('Config');
		$config = $config['cache_manager'];
		$strategyClass = $config['strategy'];

		$strategy = new $strategyClass();
		$manager = new CacheManager();

		$manager->setStrategy($strategy);

		return $manager;
	}
}