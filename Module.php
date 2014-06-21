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
				'driver' => '',
				'database' => '',
				'hostname' => '',
				'username' => '',
				'password' => '',

				'default_pk_name' => 'id',

				'mapper' => [

				],
			],

			'service_manager' => [
				'factories' => [
					'Repository\Connector' => 'Developer\PersistenceLayer\Connection\AdapterFactory',
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