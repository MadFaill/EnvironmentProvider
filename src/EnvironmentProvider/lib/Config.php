<?php
/**
 * Project: EnvironmentProvider
 * User: MadFaill
 * Date: 06.08.14
 * Time: 21:33
 * File: Config.php
 * Package: EnvironmentProvider\lib
 *
 */
namespace EnvironmentProvider\lib;
use EnvironmentProvider\error\Exception;

/**
 * Class        Config
 * @description None.
 *
 * @author      MadFaill
 * @copyright   MadFaill 06.08.14
 * @since       06.08.14
 * @version     0.01
 * @package     EnvironmentProvider\lib
 */
final class Config
{
	/** @var array  */
	private $config;

	/**
	 * @param array $options
	 */
	private function __construct(array $options)
	{
		$this->config = $options;
	}

	/**
	 * @param $path
	 * @return Config
	 * @throws \EnvironmentProvider\error\Exception
	 */
	public static function initWithIniPath($path)
	{
		if (!file_exists($path)) {
			throw new Exception('Ini file not found');
		}

		$options = parse_ini_file($path, true);
		$config  = new Config($options);

		return $config;
	}

	/**
	 * @param null $group
	 * @param null $key
	 * @param null $sub_key
	 * @return array|null
	 */
	public function get($group = Null, $key=Null, $sub_key=Null)
	{
		if ($group) {
			$data = isset($this->config[$group]) ? $this->config[$group] : null;
			if ($key)
			{
				$data = isset($data[$key]) ? $data[$key] : null;
				if ($sub_key) {
					return isset($data[$sub_key]) ? $data[$sub_key] : null;
				}

				return $data;
			}
			else {
				return $data;
			}
		}

		return $this->config;
	}
}

// ---------------------------------------------------------------------------------------------------------------------
// > END Config < #
// --------------------------------------------------------------------------------------------------------------------- 