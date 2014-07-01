<?php
namespace Developer\PersistenceLayer;

use Developer\Stuff\Hydrators\ValuesBinder;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Metadata\Metadata;
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
	EntityProducerInterface
{
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
	protected function getSqlObject()
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

	protected function loadRawBy(Where $where, QuerySettings $settings = null)
	{
		$sql = $this->getSqlObject();
		$select = $sql->select();
		$select->where($where);

		if ($settings)
		{
			if ($settings->limit) $select->limit($settings->limit);
			if ($settings->offset) $select->offset($settings->offset);
			if ($settings->order) $select->order($settings->order);
			if ($settings->group) $select->group($settings->group);
		}

		$statement = $sql->prepareStatementForSqlObject($select);
		return $statement->execute();
	}

	protected function existsBy(Where $where)
	{
		$settings = new QuerySettings();
		$settings->limit = 1;

		$result = $this->loadRawBy($where, $settings);
		return (bool) $result->current();
	}

	protected function countBy(Where $where)
	{
		$result = $this->loadRawBy($where);
		return $result->count();
	}

	protected function loadBy(Where $where, QuerySettings $settings = null)
	{
		if ($settings === null)
		{
			$settings = new QuerySettings();
		}

		$settings->limit = 1;

		return $this->prepareRow($this->loadRawBy($where, $settings));
	}

	protected function loadAllBy(
		Where $where,
		QuerySettings $settings = null,
		$returnIterator = false
	)
	{
		$result = $this->loadRawBy($where, $settings);

		if ($returnIterator)
		{
			return $this->prepareResultIterator($result);
		}

		return $this->prepareResultArray($result);
	}

	protected function deleteBy(Where $where)
	{
		$sql = $this->getSqlObject();
		$delete = $sql->delete();

		$delete->where($where);

		$statement = $sql->prepareStatementForSqlObject($delete);
		$statement->execute();
	}

	protected function updateBy(Where $where, array $data)
	{
		$sql = $this->getSqlObject();
		$update = $sql->update();

		$update->where($where);
		$update->set($data);

		$statement = $sql->prepareStatementForSqlObject($update);
		$statement->execute();
	}

	public function load($primKey)
	{
		$where = new Where();
		$where->equalTo($this->getPkName(), $primKey);
		return $this->loadBy($where);
	}

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
		$statement = $sql->prepareStatementForSqlObject($select);
		return $statement->execute()->count();
	}

	public function delete($primKey)
	{
		$where = new Where();
		$where->equalTo($this->getPkName(), $primKey);
		$this->deleteBy($where);
	}

	protected function prepareResultArray(ResultInterface $result)
	{
		return iterator_to_array($this->prepareResultIterator($result));
	}

	protected function prepareResultIterator(ResultInterface $result)
	{
		return new ResultIterator($result, $this);
	}

	protected function prepareRow(ResultInterface $result)
	{
		if (!$row = $result->current()) return null;
		$entity = $this->createEntity();

		(new ValuesBinder())->hydrate($row, $entity);
		return $entity;
	}
}