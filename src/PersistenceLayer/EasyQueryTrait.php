<?php
namespace Developer\PersistenceLayer;
use Developer\Stuff\Hydrators\ValuesBinder;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Sql\Where;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
trait EasyQueryTrait 
{
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