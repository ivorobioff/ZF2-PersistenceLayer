<?php
namespace Developer\PersistenceLayer;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Where;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
abstract class BaseSqlMapper implements
	EntityProducerInterface,
	SqlObjectProviderInterface
{
	private $resultFactory;

	/**
	 * @return ResultFactory
	 */
	protected function getResultFactory()
	{
		if (is_null($this->resultFactory))
		{
			$this->resultFactory = new ResultFactory($this);
		}

		return $this->resultFactory;
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
		$sql = $this->getSqlObject();
		$select = $sql->select();
		$select->where($where);
		$select->limit(1);
		$select->columns(['count' => new Expression('COUNT(*)')]);
		$statement = $sql->prepareStatementForSqlObject($select);
		$result = $statement->execute()->current();
		return $result['count'];
	}

	protected function loadBy(Where $where, QuerySettings $settings = null)
	{
		if ($settings === null)
		{
			$settings = new QuerySettings();
		}

		$settings->limit = 1;

		$result = $this->loadRawBy($where, $settings);

		if (!$row = $result->current()) return null;

		return $this->prepareRow($row);
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

	protected function prepareResultIterator($result)
	{
		return $this->getResultFactory()->prepareResultIterator($result);
	}

	protected function prepareResultArray($result)
	{
		return $this->getResultFactory()->prepareResultArray($result);
	}

	protected function prepareRow(array $result)
	{
		return $this->getResultFactory()->prepareRow($result);
	}
} 