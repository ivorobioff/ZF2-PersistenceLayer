<?php
namespace Developer\PersistenceLayer\Commands;
use Zend\Db\Adapter\Adapter;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class MultipleInsertCommand
{
	private $adapter;
	private $tableName;

	public function __construct(Adapter $adapter, $tableName)
	{
		$this->adapter = $adapter;
		$this->tableName = $tableName;
	}


	public function execute($data)
	{
		if (!$data) return null;
		$this->adapter->query($this->prepareQuery($data))->execute();
	}

	private function prepareQuery($data)
	{
		$platform = $this->adapter->getPlatform();
		$quotedTable =	$platform->quoteIdentifier($this->tableName);

		$fields = '';
		foreach (array_keys(reset($data)) as $field)
		{
			$fields .= ','.$platform->quoteIdentifier($field);
		}

		$fields = ltrim($fields, ',');

		$values = '';
		foreach ($data as $row)
		{
			$values .= ',('.$this->quoteValueList(array_values($row)).')';
		}

		$values = ltrim($values, ',');

		return 'INSERT INTO '.$quotedTable.' ('.$fields.') VALUES '.$values;
	}

	/**
	 * The original method has a bug, so it is the alternative for it
	 * @param array $valueList
	 * @return string
	 */
	private function quoteValueList($valueList)
	{
		$platform = $this->adapter->getPlatform();

		foreach ($valueList as $key => $value)
		{
			$valueList[$key] = $platform->quoteValue($value);
		}
		return implode(', ', $valueList);
	}
} 