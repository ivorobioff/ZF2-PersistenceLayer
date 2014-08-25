<?php
namespace Developer\PersistenceLayer;
use Developer\PersistenceLayer\DisposableRequest\DisposableRequestInterface;
use Zend\Db\Sql\Sql;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
abstract class AbstractDisposableRequest implements
	DisposableRequestInterface,
	EntityProducerInterface,
	SqlObjectProviderInterface,
	RepositoryAwareInterface
{
	use EasyQueryTrait;

	private $repository;

	public function createEntity()
	{
		return $this->getRepository()->createEntity();
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
			throw new \InvalidArgumentException('Mapper must implement EntityProducerInterface or SqlObjectProviderInterface');
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