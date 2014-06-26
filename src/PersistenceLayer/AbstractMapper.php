<?php
namespace Developer\PersistenceLayer;

use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Metadata\Metadata;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
abstract class AbstractMapper implements ServiceLocatorAwareInterface, MapperInterface
{
	private $serviceLocator;
	private $sqlObject;

	/**
	 * @var mixed
	 */
	private $pkName;

	private $tableName;

	/**
	 * @return EntityInterface
	 */
	abstract public function createEntity();

	protected function getPkName()
	{
		if (is_null($this->pkName))
		{
			$this->pkName = $this->getServiceLocator()
				->get('Config')['repository']['default_pk_name'];
		}

		return $this->pkName;
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
				$this->getServiceLocator()->get('Repository\Connector'),
				$this->getTableName()
			);
		}

		return $this->sqlObject;
	}

	protected function getTableName()
	{
		if (is_null($this->tableName))
		{
			$config = $this->getServiceLocator()->get('config')['repository'];
			$classArray = explode('\\', get_class($this));
			$class = end($classArray);

			$this->tableName = $config['mapper'][$class];
		}

		return $this->tableName;
	}

	public function save(EntityInterface $entity)
	{
		$metadata = new Metadata($this->getSqlObject()->getAdapter());
		$column_names = $metadata->getColumnNames($this->getSqlObject()->getTable());

		$values = [];

		foreach ($column_names as $name)
		{
			if ($name == $this->getPkName()) continue ;
			$values[$name] = $entity->$name;
		}

		if ($entity->{$this->getPkName()} === null)
		{
			$query = $this->prepareInsert($values);
		}
		else
		{
			$query = $this->prepareUpdate($values, $entity->{$this->getPkName()});
		}

		$statement = $this->getSqlObject()->prepareStatementForSqlObject($query);
		$result = $statement->execute();

		$id = $result->getGeneratedValue();
		$entity->{$this->getPkName()} = $id;
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

	protected function loadRawBy(Where $where, $limit = null, $offset = null)
	{
		$sql = $this->getSqlObject();
		$select = $sql->select();
		$select->where($where);

		if ($limit) $select->limit($limit);
		if ($offset) $select->offset($offset);

		$statement = $sql->prepareStatementForSqlObject($select);
		return $statement->execute();
	}

	protected function existsBy(Where $where)
	{
		$result = $this->loadRawBy($where, 1);
		return (bool) $result->current();
	}

	protected function countBy(Where $where)
	{
		$result = $this->loadRawBy($where);
		return $result->count();
	}

	protected function loadBy(Where $where, $offset = null)
	{
		return $this->prepareRow($this->loadRawBy($where, 1, $offset));
	}

	protected function loadAllBy(Where $where, $limit = null, $offset = null)
	{
		return $this->prepareResult($this->loadRawBy($where, $limit, $offset));
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
		return $this->prepareResult($statement->execute());
	}

	public function count()
	{
		$sql = $this->getSqlObject();
		$select = $sql->select();
		$statement = $sql->prepareStatementForSqlObject($select);
		return $statement->execute()->count();
	}

	public function delete(EntityInterface $entity)
	{
		$primKey = $entity->{$this->getPkName()};
		if ($primKey === null) throw new \RuntimeException('Primary key is undefined');

		$where = new Where();
		$where->equalTo($this->getPkName(), $primKey);
		$this->deleteBy($where);

		$entity->{$this->getPkName()} = null;
	}

	protected function prepareResult(ResultInterface $result)
	{
		$return = [];

		foreach ($result as $row)
		{
			$return[] = $this->arrayToEntity($row);
		}

		return $return;
	}

	protected function prepareRow(ResultInterface $result)
	{
		if (!$row = $result->current()) return null;
		return $this->arrayToEntity($row);
	}

	private function arrayToEntity(array $row)
	{
		$item = $this->createEntity();

		foreach ($row as $name => $value)
		{
			$item->$name = $value;
		}

		return $item;
	}
}