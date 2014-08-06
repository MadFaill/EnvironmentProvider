<?php
/**
 * Project: EnvironmentProvider
 * User: MadFaill
 * Date: 06.08.14
 * Time: 21:33
 * File: Environ.php
 * Package: EnvironmentProvider
 *
 */
namespace EnvironmentProvider\lib;

/**
 * Class        Environ
 * @description None.
 *
 * @author      MadFaill
 * @copyright   MadFaill 06.08.14
 * @since       06.08.14
 * @version     0.01
 * @package     EnvironmentProvider
 */
class Environ 
{
	/** @var \EnvironmentProvider\lib\Config  */
	private $config;

	private $environment_mapping = array();
	private $fallback;
	private $ini_path;
	private $environ;
	private $data = array();

	/**
	 * @param array $environment_mapping
	 */
	public function __construct(array $environment_mapping)
	{
		$this->data['user']            = isset($_SERVER['USER']) ? $_SERVER['USER'] : 'web';
		$this->data['is_console']      = $this->data['user'] != 'web';
		$this->data['domain']          = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'console';
		$this->data['server_ip']       = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : null;
		$this->data['server_software'] = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : null;
		$this->data['client_ip']       = null;
		$this->data['client_agent']    = null;

		if ($this->isWeb()) {
			if (isset($_SERVER['REMOTE_ADDR'])) {
				$this->data['client_ip'] = $_SERVER['REMOTE_ADDR'];
			}

			if (isset($_SERVER['X_FORWARDED_FOR'])) {
				$this->data['client_ip'] = $_SERVER['X_FORWARDED_FOR'];
			}

			if (isset($_SERVER['HTTP_USER_AGENT'])) {
				$this->data['client_agent'] = $_SERVER['HTTP_USER_AGENT'];
			}
		}


		$this->fallback = $environment_mapping['settings']['fallback'];
		$this->ini_path = $environment_mapping['settings']['config_path'];

		foreach ($environment_mapping as $environ => $data) {
			if (isset($data['scan'])) {
				$this->environment_mapping[$environ] = $data['scan'];
			}
		}
	}

	/**
	 * @return bool
	 */
	public function isWeb()
	{
		return !$this->data['is_console'];
	}

	/**
	 * @return Config
	 */
	public function config()
	{
		if (!$this->config) {
			$this->config = Config::initWithIniPath($this->_config_ini_file());
		}

		return $this->config;
	}

	/**
	 * @return string
	 */
	public function current()
	{
		if (!$this->environ)
		{
			foreach ($this->environment_mapping as $env => $search)
			{
				if (   in_array($this->data['server_ip'], $search)
					|| in_array($this->data['domain'], $search)
					|| in_array($this->data['user'], $search))
				{
					$this->environ = $env;
					break;
				}
			}

			if (!$this->environ) {
				$this->environ = $this->fallback;
			}
		}
		return $this->environ;
	}

	/**
	 * @return string
	 */
	private function _config_ini_file()
	{
		return sprintf("%s/%s.ini", $this->ini_path, $this->current());
	}
}

// ---------------------------------------------------------------------------------------------------------------------
// > END Environ < #
// --------------------------------------------------------------------------------------------------------------------- 