<?php
namespace Developer\PersistenceLayer;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class CallbackResultIterator extends \IteratorIterator
{
	private $callback;

	public function __construct(\Traversable $iterator, callable $callback)
	{
		parent::__construct($iterator);

		if (!is_callable($callback))
		{
			throw new \InvalidArgumentException('Callback argument must be callable');
		}

		$this->callback = $callback;
	}

	public function current()
	{
		return call_user_func($this->callback, parent::current());
	}
} 