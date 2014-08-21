<?php
namespace Developer\PersistenceLayer\Query;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
abstract class AbstractQuery
{
	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE = 1;

	const HAVING_ORDER_WAY = 1;

	private $propertyNames;

	public function __construct()
	{
		$names = $this->getPropertyNames();

		foreach ($names as $name)
		{
			$this->{$name} = new Property($name);
		}
	}

	/**
	 * @param null|string $filter
	 * @return Property[]|array
	 */
	public function getProperties($filter = null)
	{
		$names = $this->getPropertyNames();

		if ($filter === self::HAVING_ORDER_WAY)
		{
			$result = new PriorityHeap();

			foreach ($names as $name)
			{
				if ($this->{$name}->hasOrderWay())
				{
					$result->insert($this->{$name});
				}
			}

			return iterator_to_array($result);
		}

		$result = [];

		foreach ($names as $name)
		{
			$result[] = $this->{$name};
		}

		return $result;
	}

	/**
	 * @return array
	 */
	private function getPropertyNames()
	{
		if (is_null($this->propertyNames))
		{
			$this->propertyNames = [];

			$refObject = new \ReflectionObject($this);
			$refProperties = $refObject->getProperties(\ReflectionProperty::IS_PUBLIC);

			foreach ($refProperties as $refProperty)
			{
				$this->propertyNames[] = $refProperty->getName();
			}

		}

		return $this->propertyNames;
	}
} 