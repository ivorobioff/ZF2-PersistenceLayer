<?php
namespace Developer\PersistenceLayer\Cache;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
trait CacheManagerAwareTrait 
{
	private $cacheManager;

	/**
	 * @return CacheManagerInterface
	 */
	public function getCacheManager()
	{
		return $this->cacheManager;
	}

	public function setCacheManager(CacheManagerInterface $cacheManager)
	{
		$this->cacheManager = $cacheManager;
	}
} 