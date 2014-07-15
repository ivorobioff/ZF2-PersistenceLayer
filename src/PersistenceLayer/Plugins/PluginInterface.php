<?php
namespace Developer\PersistenceLayer\Plugins;
use Developer\PersistenceLayer\MapperInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface PluginInterface 
{
	public function setRepository(MapperInterface $mapper);
	public function getRepository();
} 