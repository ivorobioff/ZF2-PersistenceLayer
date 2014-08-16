<?php
namespace Developer\PersistenceLayer\Cache;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
trait StaticCacheManagerAwareTrait
{
	private $staticCacheManager;

	/**
	 * @return CacheManager
	 */
	public function getStaticCacheManager()
	{
		return $this->staticCacheManager;
	}

	public function setStaticCacheManager(CacheManagerInterface $cacheManager)
	{
		return $this->staticCacheManager = $cacheManager;
	}
} 