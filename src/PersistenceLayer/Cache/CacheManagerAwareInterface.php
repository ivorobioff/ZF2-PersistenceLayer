<?php
namespace Developer\PersistenceLayer\Cache;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface CacheManagerAwareInterface
{
	/**
	 * @return CacheManagerInterface
	 */
	public function getCacheManager();
	public function setCacheManager(CacheManagerInterface $cacheManager);
} 