<?php
namespace Developer\PersistenceLayer\MongoDB;
use Developer\PersistenceLayer\EntityInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
abstract class AbstractEntity implements EntityInterface
{
	public $_id;
} 