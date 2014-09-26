<?php
namespace Redaxscript\Modules\Maps;

use Redaxscript\Registry;

/**
 * integrate social buttons
 *
 * @since 2.2.0
 *
 * @package Redaxscript
 * @category Modules
 * @author Henry Ruhs
 */

class Maps extends Config
{
	/**
	 * custom module setup
	 *
	 * @var array
	 */

	protected static $_module = array(
		'name' => 'Maps',
		'alias' => 'Maps',
		'author' => 'Redaxmedia',
		'description' => 'Integrate Google Maps',
		'version' => '2.2.0',
		'status' => 1,
		'access' => 0
	);

	/**
	 * loaderStart
	 *
	 * @since 2.2.0
	 */

	public static function loaderStart()
	{
		if (!Registry::get('adminParameter'))
		{
			global $loader_modules_styles, $loader_modules_scripts;
			$loader_modules_styles[] = 'modules/maps/styles/maps.css';
			$loader_modules_scripts[] = 'modules/maps/scripts/startup.js';
			$loader_modules_scripts[] = 'modules/maps/scripts/maps.js';
		}
	}

	/**
	 * scriptsEnd
	 *
	 * @since 2.2.0
	 */

	public static function scriptsEnd()
	{
		if (!Registry::get('adminParameter'))
		{
			$output = '<script src="' . self::$_config['apiUrl'] . '?key=' .  self::$_config['apiKey'] . '&amp;sensor=' . self::$_config['sensor'] . '"></script>' . PHP_EOL;
			echo $output;
		}
	}

	/**
	 * render
	 *
	 * @since 2.2.0
	 */

	public static function _render($lat = 0, $lng = 0, $zoom = 0)
	{
		$output = '<div class="js_map map"';
		if (is_numeric($lat))
		{
			$output .= ' data-lat="' . $lat . '"';
		}
		if (is_numeric($lng))
		{
			$output .= ' data-lng="' . $lng . '"';
		}
		if (is_numeric($zoom))
		{
			$output .= ' data-zoom="' . $zoom . '"';
		}
		$output .= '></div>';
		echo $output;
	}
}
