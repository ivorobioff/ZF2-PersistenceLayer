<?php
namespace Developer\PersistenceLayer;
use Developer\PersistenceLayer\Plugins\PluginInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
abstract class AbstractPlugin extends BaseSqlMapper implements PluginInterface, ServiceLocatorAwareInterface
{
	use ServiceLocatorAwareTrait;

	private $repository;

	public function setRepository(MapperInterface $repository)
	{
		$this->repository = $repository;
	}

	/**
	 * @return AbstractMapper
	 */
	public function getRepository()
	{
		return $this->repository;
	}

	public function createEntity()
	{
		return $this->getRepository()->createEntity();
	}

	public function getHydrator()
	{
		return $this->getRepository()->getHydrator();
	}

	public function getSqlObject()
	{
		return $this->getRepository()->getSqlObject();
	}
}