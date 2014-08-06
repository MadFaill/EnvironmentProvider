<?php
/**
 * Project: EnvironmentProvider
 * User: MadFaill
 * Date: 06.08.14
 * Time: 21:34
 * File: Provider.php
 * Package: EnvironmentProvider
 *
 */
namespace EnvironmentProvider;
use EnvironmentProvider\error\Exception;
use EnvironmentProvider\lib\Config;
use EnvironmentProvider\lib\Environ;

/**
 * Class        Provider
 * @description None.
 *
 * @author      MadFaill
 * @copyright   MadFaill 06.08.14
 * @since       06.08.14
 * @version     0.01
 * @package     EnvironmentProvider
 */
class Provider 
{
	/** @var \EnvironmentProvider\lib\Environ  */
	private $environ;

	/**
	 * @param Environ $environment
	 */
	private function __construct(Environ $environment)
	{
		$this->environ = $environment;
	}

	/**
	 * Инициализирует среду с INI конфигом
	 *
	 * @param $path
	 * @return Provider
	 * @throws error\Exception
	 */
	public static function initWithINIFile($path)
	{
		if (!file_exists($path)) {
			throw new Exception('INI file not found');
		}

		define('_PROVIDER_INI_FILE_PATH_', dirname($path));

		$environ_map = parse_ini_file($path, true);

		$environ = new Environ($environ_map);
		$provider = new Provider($environ);

		return $provider;
	}

	/**
	 * @return Environ
	 */
	public function Environ()
	{
		return $this->environ;
	}

	/**
	 * @return Config
	 */
	public function Config()
	{
		return $this->environ->config();
	}
}

// ---------------------------------------------------------------------------------------------------------------------
// > END Provider < #
// --------------------------------------------------------------------------------------------------------------------- 