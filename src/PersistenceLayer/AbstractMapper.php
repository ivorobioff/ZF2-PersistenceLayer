<?php
namespace Developer\PersistenceLayer;

use Developer\PersistenceLayer\Cache\StaticCacheManagerAwareInterface;
use Developer\PersistenceLayer\Cache\StaticCacheManagerAwareTrait;
use Developer\PersistenceLayer\DisposableRequest\DisposableRequestInterface;
use Developer\PersistenceLayer\Plugins\PluginsConfigAwareTrait;
use Developer\PersistenceLayer\Plugins\PluginsProviderInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Stdlib\Hydrator\ObjectProperty;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
abstract class AbstractMapper extends BaseSqlMapper implements
	MapperInterface,
	ServiceLocatorAwareInterface,
	PluginsProviderInterface,
	StaticCacheManagerAwareInterface,
	DisposableRequestCapableInterface
{
	use ServiceLocatorAwareTrait;
	use PluginsConfigAwareTrait;
	use StaticCacheManagerAwareTrait;

	private $sqlObject;

	/**
	 * @var mixed
	 */
	private $pkName;
	private $tableName;
	private $adapter;

	public function __construct($pkName, $tableName, Adapter $adapter)
	{
		$this->tableName = $tableName;
		$this->pkName = $pkName;
		$this->adapter = $adapter;
	}

	public function getHydrator()
	{
		return new ObjectProperty();
	}

	public function getPkName()
	{
		return $this->pkName;
	}

	public function getTableName()
	{
		return $this->tableName;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	/**
	 * @return Sql
	 */
	public function getSqlObject()
	{
		if (is_null($this->sqlObject))
		{
			$this->sqlObject = new Sql(
				$this->getAdapter(),
				$this->getTableName()
			);
		}

		return $this->sqlObject;
	}

	public function save(EntityInterface $entity)
	{
		$object = $entity;

		if ($entity instanceof StoragableEntityInterface)
		{
			$object = $entity->getStorage();
		}

		$hydrator = $this->getHydrator();
		$values = $hydrator->extract($object);

		$pkValue = $values[$this->getPkName()];
		unset($values[$this->getPkName()]);

		if ($pkValue === null)
		{
			$query = $this->prepareInsert($values);
		}
		else
		{
			$query = $this->prepareUpdate($values, $pkValue);
		}

		$statement = $this->getSqlObject()->prepareStatementForSqlObject($query);
		$result = $statement->execute();

		if ($pkValue === null)
		{
			$id = $result->getGeneratedValue();
			$hydrator->hydrate([$this->getPkName() => $id], $object);
		}
	}

	private function prepareUpdate(array $values, $primKey)
	{
		$update = $this->getSqlObject()->update();
		$where = new Where();
		$where->equalTo($this->getPkName(), $primKey);
		$update->where($where);
		$update->set($values);

		return $update;
	}

	private function prepareInsert(array $values)
	{
		$insert = $this->getSqlObject()->insert();
		$insert->values($values);

		return $insert;
	}

	public function load($primKey)
	{
		$where = new Where();
		$where->equalTo($this->getPkName(), $primKey);
		return $this->loadBy($where);
	}

	/**
	 * @return array
	 */
	public function loadAll()
	{
		$sql = $this->getSqlObject();
		$select = $sql->select();
		$statement = $sql->prepareStatementForSqlObject($select);
		return $this->prepareResultArray($statement->execute());
	}

	public function count()
	{
		$sql = $this->getSqlObject();
		$select = $sql->select();
		$select->columns(['count' => new Expression('COUNT(*)')]);
		$select->limit(1);
		$statement = $sql->prepareStatementForSqlObject($select);
		$result = $statement->execute()->current();
		return $result['count'];
	}

	public function delete(EntityInterface $entity)
	{
		$primKey = $entity->{$this->getPkName()};
		$where = new Where();
		$where->equalTo($this->getPkName(), $primKey);
		$this->deleteBy($where);

		$entity->{$this->getPkName()} = null;
	}

	/**
	 * @param $name
	 * @return AbstractPlugin
	 * @throws \BadMethodCallException
	 */
	public function getPlugin($name)
	{
		$config = $this->getPluginsConfig();

		if (!isset($config[$name]))
		{
			throw new  \BadMethodCallException('Plugin "'.$name.'" does NOT exist');
		}

		/**
		 * @var AbstractPlugin|ServiceLocatorAwareInterface $plugin
		 */
		$plugin = new $config[$name]();
		$plugin->setRepository($this);

		if ($plugin instanceof ServiceLocatorAwareInterface)
		{
			$plugin->setServiceLocator($this->getServiceLocator());
		}

		return $plugin;
	}

	/**
	 * @param DisposableRequestInterface|RepositoryAwareInterface $request
	 * @return mixed
	 */
	public function executeDisposableRequest(DisposableRequestInterface $request)
	{
		$request->setRepository($this);
		return $request->getResult();
	}
}