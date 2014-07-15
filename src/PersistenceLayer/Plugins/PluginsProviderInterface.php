<?php
namespace Developer\PersistenceLayer\Plugins;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
interface PluginsProviderInterface
{
	public function setPluginsConfig(array $config);
	public function getPluginsConfig();

	/**
	 * @param $name
	 * @return PluginInterface
	 */
	public function getPlugin($name);
} 