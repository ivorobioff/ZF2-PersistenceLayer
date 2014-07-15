<?php
namespace Developer\PersistenceLayer;
use Zend\Db\Sql\Sql;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface SqlObjectProviderInterface
{
	/**
	 * @return Sql
	 */
	public function getSqlObject();
} 