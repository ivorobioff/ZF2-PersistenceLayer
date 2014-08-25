<?php
namespace Developer\PersistenceLayer;
use Developer\PersistenceLayer\DisposableRequest\DisposableRequestInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface DisposableRequestCapableInterface 
{
	public function executeDisposableRequest(DisposableRequestInterface $request);
} 