<?php
namespace Developer\PersistenceLayer\Cache;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface StaticCacheManagerAwareInterface
{
	public function setStaticCacheManager(StaticCacheManager $cacheManager);

	/**
	 * @return StaticCacheManager
	 */
	public function getStaticCacheManager();
} 