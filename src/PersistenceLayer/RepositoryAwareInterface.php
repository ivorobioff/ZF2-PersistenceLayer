<?php
namespace Developer\PersistenceLayer;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface RepositoryAwareInterface 
{
	public function setRepository(MapperInterface $mapper);
	public function getRepository();
} 