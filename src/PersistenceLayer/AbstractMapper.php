<?php
namespace Developer\PersistenceLayer;

use Developer\PersistenceLayer\Cache\StaticCacheManagerAwareInterface;
use Developer\PersistenceLayer\Cache\StaticCacheManagerAwareTrait;
use Developer\PersistenceLayer\Plugins\PluginsConfigAwareTrait;
use Developer\PersistenceLayer\Plugins\PluginsProviderInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Metadata\Metadata;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
abstract class AbstractMapper implements
	ServiceLocatorAwareInterface,
	MapperInterface,
	EntityProducerInterface,
	PluginsProviderInterface,
	SqlObjectProviderInterface,
	StaticCacheManagerAwareInterface
{
	use PluginsConfigAwareTrait;
	use EasyQueryTrait;
	use StaticCacheManagerAwareTrait;

	private $serviceLocator;
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
	 * Set service locator
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 */
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
		$this->serviceLocator = $serviceLocator;
	}

	/**
	 * Get service locator
	 *
	 * @return ServiceLocatorInterface
	 */
	public function getServiceLocator()
	{
		return $this->serviceLocator;
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
		$metadata = new Metadata($this->getAdapter());
		$column_names = $metadata->getColumnNames($this->getSqlObject()->getTable());

		$values = [];

		foreach ($column_names as $name)
		{
			if ($name == $this->getPkName()) continue ;
			$values[$name] = $entity->$name;
		}

		$isInsert = $entity->{$this->getPkName()} === null;

		if ($isInsert)
		{
			$query = $this->prepareInsert($values);
		}
		else
		{
			$query = $this->prepareUpdate($values, $entity->{$this->getPkName()});
		}

		$statement = $this->getSqlObject()->prepareStatementForSqlObject($query);
		$result = $statement->execute();

		if ($isInsert)
		{
			$id = $result->getGeneratedValue();
			$entity->{$this->getPkName()} = $id;
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
	 * @param null $offset
	 * @param null $limit
	 * @param bool $returnIterator
	 * @return array|ResultIterator
	 */
	public function loadAll($offset = null, $limit = null, $returnIterator = false)
	{
		$sql = $this->getSqlObject();
		$select = $sql->select();

		if ($offset !== null)
		{
			$select->offset($offset);
		}

		if ($limit !== null)
		{
			$select->limit($limit);
		}

		$statement = $sql->prepareStatementForSqlObject($select);

		if ($returnIterator === true)
		{
			return $this->prepareResultIterator($statement->execute());
		}

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
		 * @var AbstractPlugin $plugin
		 */
		$plugin = new $config[$name]();
		$plugin->setRepository($this);

		if ($plugin instanceof ServiceLocatorAwareInterface)
		{
			$plugin->setServiceLocator($this->getServiceLocator());
		}

		return $plugin;
	}
}