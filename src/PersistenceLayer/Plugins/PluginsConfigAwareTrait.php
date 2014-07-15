<?php
namespace Developer\PersistenceLayer\Plugins;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
trait PluginsConfigAwareTrait
{
	private $pluginConfig;

	public function setPluginsConfig(array $config)
	{
		$this->pluginConfig = $config;
	}

	public function getPluginsConfig()
	{
		return $this->pluginConfig;
	}
}