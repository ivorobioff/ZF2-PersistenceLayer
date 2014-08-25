<?php
namespace Developer\PersistenceLayer;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class AbstractEntity implements EntityInterface
{
	public function exchangeArray($data)
	{
		foreach ($data as $name => $value)
		{
			$this->{$name} = $value;
		}
	}

	public function getArrayCopy()
	{
		$propertiesArray = [];

		$refObject = new \ReflectionObject($this);
		$refProperties = $refObject->getProperties(\ReflectionProperty::IS_PUBLIC);

		foreach ($refProperties as $refProperty)
		{
			$propertiesArray[$refProperty->getName()] = $refProperty->getValue($this);
		}

		return $propertiesArray;
	}
}