<?php
namespace Developer\PersistenceLayer;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{
	/**
	 * Returns configuration to merge with application configuration
	 *
	 * @return array|\Traversable
	 */
	public function getConfig()
	{
		return [
			'repository' => [
				'default' => [
					'adapter' => '',
					'default_pk_name' => 'id',
					'factory' => 'Developer\PersistenceLayer\MapperFactory',
					'services' => []
				]
			],

			'service_manager' => [
				'abstract_factories' => [
					'Developer\PersistenceLayer\AbstractRepositoryFactory',
				]
			]
		];
	}

	/**
	 * Return an array for passing to Zend\Loader\AutoloaderFactory.
	 *
	 * @return array
	 */
	public function getAutoloaderConfig()
	{
		return [
			'Zend\Loader\StandardAutoloader' =>[
				'namespaces' => [
					__NAMESPACE__ => __DIR__ . '/src/PersistenceLayer',
				],
			]
		];
	}
}