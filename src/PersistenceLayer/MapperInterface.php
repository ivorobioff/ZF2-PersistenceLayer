<?php
namespace Developer\PersistenceLayer;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface MapperInterface
{
	public function save(EntityInterface $entity);
	public function load($primKey);
	public function loadAll();
	public function count();
	public function delete(EntityInterface $entity);
} 