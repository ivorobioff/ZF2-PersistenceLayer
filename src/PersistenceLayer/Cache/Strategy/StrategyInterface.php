<?php
namespace Developer\PersistenceLayer\Cache\Strategy;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface StrategyInterface
{
	public function has($method, array $args);
	public function get($method, array $args);
	public function add($method, array $args, $value);
	public function clear($method, array $args);
	public function clearAll();
} 