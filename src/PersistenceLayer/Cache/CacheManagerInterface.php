<?php
namespace Developer\PersistenceLayer\Cache;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface CacheManagerInterface
{
	public function call($method);
	public function clear($method);
	public function clearAll();
} 