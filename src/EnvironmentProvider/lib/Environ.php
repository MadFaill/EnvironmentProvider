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
	private $environment_match = array();
	private $environment_env = array();
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
		$this->data['is_console']      = !isset($_SERVER['HTTP_HOST']) && !isset($_SERVER['REMOTE_ADDR']); // fix if CGI! <==$this->data['user'] != 'web';
		$this->data['domain']          = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'console';
		$this->data['server_ip']       = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : null;
		$this->data['server_software'] = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : null;
		$this->data['path']            = $_SERVER['SCRIPT_FILENAME'];
		$this->data['pwd']             = isset($_SERVER['PWD']) ? $_SERVER['PWD'] : '';
		$this->data['home']            = isset($_SERVER['HOME']) ? $_SERVER['HOME'] : '';
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

		foreach ($environment_mapping as $environ => $data)
		{
			// fill scan
			if (isset($data['scan'])) {
				$this->environment_mapping[$environ] = $data['scan'];
			}

			// fill match
			if (isset($data['match'])) {
				$this->environment_match[$environ] = $data['match'];
			}

			// by env key
			if (isset($data['env'])) {
				$this->environment_env[$environ] = $data['env'];
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
		if (!$this->environ) {
			$this->environ = $this->_detect_env();
		}

		return $this->environ;
	}

	/**
	 *
	 * @param null $key
	 * @return mixed
	 */
	public function data($key=null)
	{
		return $key ? (isset($this->data[$key]) ? $this->data[$key] : null) : null;
	}

	/**
	 * @return string
	 */
	private function _config_ini_file()
	{
		return sprintf("%s/%s.ini", $this->ini_path, $this->current());
	}

	/**
	 * @return string
	 */
	private function _detect_env()
	{
		// try env key
		foreach ($this->environment_env as $env => $environ)
		{
			foreach ($environ as $eKey => $eValue) {
				if (isset($_ENV[$eKey]) && ($_ENV[$eKey] == $eValue) ) {
					return $env;
				}
			}
		}

		// try scan
		foreach ($this->environment_mapping as $env => $search)
		{
			if (   in_array($this->data['server_ip'], $search)
				|| in_array($this->data['domain'], $search)
				|| in_array($this->data['user'], $search))
			{
				return $env;
			}
		}

		// try match
		foreach ($this->environment_match as $env => $match)
		{
			foreach ($this->data as $dKey => $dVal)
			{
				if (isset($match[$dKey]))
				{
					$pattern = $match[$dKey];
					if (preg_match("#($pattern)#ius", $dVal)) {
						return $env;
					}
				}
			}
		}

		return $this->fallback;
	}
}

// ---------------------------------------------------------------------------------------------------------------------
// > END Environ < #
// --------------------------------------------------------------------------------------------------------------------- 
