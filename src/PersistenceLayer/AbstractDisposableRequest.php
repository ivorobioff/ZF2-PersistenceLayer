<?php
namespace Developer\PersistenceLayer;
use Developer\PersistenceLayer\DisposableRequest\DisposableRequestInterface;
use Zend\Db\Sql\Sql;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
abstract class AbstractDisposableRequest extends BaseSqlMapper implements
	ServiceLocatorAwareInterface,
	DisposableRequestInterface,
	RepositoryAwareInterface
{
	use ServiceLocatorAwareTrait;

	private $repository;

	public function createEntity()
	{
		return $this->getRepository()->createEntity();
	}

	public function getHydrator()
	{
		return $this->getRepository()->getHydrator();
	}

	/**
	 * @return Sql
	 */
	public function getSqlObject()
	{
		return $this->getRepository()->getSqlObject();
	}

	public function setRepository(MapperInterface $mapper)
	{
		if (!$mapper instanceof EntityProducerInterface
			|| !$mapper instanceof SqlObjectProviderInterface)
		{
			throw new \InvalidArgumentException('Mapper must implement EntityProducerInterface and SqlObjectProviderInterface');
		}

		$this->repository = $mapper;
	}

	/**
	 * @return MapperInterface|EntityProducerInterface|SqlObjectProviderInterface
	 */
	public function getRepository()
	{
		return $this->repository;
	}
}