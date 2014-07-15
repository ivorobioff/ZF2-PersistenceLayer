<?php
namespace Developer\PersistenceLayer;
use Developer\PersistenceLayer\Plugins\PluginInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
abstract class AbstractPlugin implements
	PluginInterface,
	EntityProducerInterface,
	SqlObjectProviderInterface
{
	use EasyQueryTrait;

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

	public function getSqlObject()
	{
		return $this->getRepository()->getSqlObject();
	}
}