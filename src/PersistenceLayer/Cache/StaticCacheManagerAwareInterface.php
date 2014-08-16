<?php
namespace Developer\PersistenceLayer\Cache;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface StaticCacheManagerAwareInterface
{
	public function setStaticCacheManager(CacheManagerInterface $cacheManager);

	/**
	 * @return CacheManagerInterface
	 */
	public function getStaticCacheManager();
} 