<?php
namespace Developer\PersistenceLayer;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
abstract class AbstractRepositoryFactory
{
	protected $serviceLocator;
	protected $repositoryName;
	protected $config;
	protected $repositoryConfig;

	public function __construct(
		$repositoryName,
		$repositoryConfig,
		$config,
		ServiceLocatorInterface $serviceLocator)
	{
		$this->serviceLocator = $serviceLocator;
		$this->repositoryName = $repositoryName;
		$this->config = $config;
		$this->repositoryConfig = $repositoryConfig;
	}

	abstract public function createRepository();
} 