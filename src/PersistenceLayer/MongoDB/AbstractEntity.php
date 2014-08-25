<?php
namespace Developer\PersistenceLayer\MongoDB;
use Developer\PersistenceLayer\EntityInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
abstract class AbstractEntity implements EntityInterface
{
	/**
	 * @var \MongoId
	 */
	public $_id;
	public function getId()
	{
		return $this->_id->{'$id'};
	}

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