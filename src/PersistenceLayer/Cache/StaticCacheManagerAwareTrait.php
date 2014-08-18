<?php
namespace Developer\PersistenceLayer\Cache;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
trait StaticCacheManagerAwareTrait
{
	private $staticCacheManager;

	/**
	 * @return StaticCacheManager
	 */
	public function getStaticCacheManager()
	{
		return $this->staticCacheManager;
	}

	public function setStaticCacheManager(StaticCacheManager $cacheManager)
	{
		return $this->staticCacheManager = $cacheManager;
	}
} 