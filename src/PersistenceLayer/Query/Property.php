<?php
namespace Developer\PersistenceLayer\Query;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class Property 
{
	const ORDER_ASC = 'asc';
	const ORDER_DESC = 'desc';

	private $name;
	private $orderWay;
	private $value;
	private $orderPriority;

	public function __construct($name)
	{
		$this->name = $name;
	}

	public function hasValue()
	{
		return $this->value !== null;
	}

	public function setValue($value)
	{
		$this->value = $value;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setOrderWay($way, $priority = 0)
	{
		$this->orderWay = $way;
		$this->orderPriority = $priority;
	}

	public function getOrderWay()
	{
		return $this->orderWay;
	}

	public function hasOrderWay()
	{
		return $this->orderWay !== null;
	}

	public function getOrderPriority()
	{
		return $this->orderPriority;
	}
} 