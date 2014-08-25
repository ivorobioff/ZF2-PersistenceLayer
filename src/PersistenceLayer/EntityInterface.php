<?php
namespace Developer\PersistenceLayer;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface EntityInterface 
{
	public function exchangeArray($data);
	public function getArrayCopy();
} 