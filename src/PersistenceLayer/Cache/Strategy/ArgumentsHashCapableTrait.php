<?php
namespace Developer\PersistenceLayer\Cache\Strategy;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
trait ArgumentsHashCapableTrait
{
	protected function prepareArgHash(array $args)
	{
		return md5(serialize($args));
	}
} 