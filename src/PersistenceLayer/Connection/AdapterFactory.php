<?php
namespace Developer\PersistenceLayer\Connection;
use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class AdapterFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$config = $serviceLocator->get('config')['repository'];

		return new Adapter([
			'driver' => $config['driver'],
			'database' => $config['database'],
			'hostname' => $config['hostname'],
			'username' => $config['username'],
			'password' => $config['password']
		]);
	}
}