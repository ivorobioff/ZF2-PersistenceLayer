<?php
namespace Developer\PersistenceLayer\Cache\Strategy;
use Zend\Session\Container;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class SessionStrategy extends StaticStrategy
{
	public function __construct()
	{
		$this->storage = new Container('developer_cache_manager');
	}
}