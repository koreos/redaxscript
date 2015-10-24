<?php
namespace Redaxscript\Modules\Gallery;

use Redaxscript\Directory;

/**
 * read external rss and atom feeds
 *
 * @since 2.3.0
 *
 * @package Redaxscript
 * @category Modules
 * @author Henry Ruhs
 */

class Gallery extends Config
{
	/**
	 * array of the module
	 *
	 * @var array
	 */

	protected static $_moduleArray = array(
		'name' => 'Gallery',
		'alias' => 'Gallery',
		'author' => 'Redaxmedia',
		'description' => 'Lightbox enhanced image gallery',
		'version' => '2.6.0'
	);

	/**
	 * loaderStart
	 *
	 * @since 2.6.0
	 */

	public static function loaderStart()
	{
		global $loader_modules_styles, $loader_modules_scripts;
		$loader_modules_styles[] = 'modules/gallery/styles/gallery.css';
		$loader_modules_styles[] = 'modules/gallery/styles/query.css';
		$loader_modules_scripts[] = 'modules/gallery/scripts/init.js';
		$loader_modules_scripts[] = 'modules/gallery/scripts/gallery.js';
	}

	/**
	 * render
	 *
	 * @since 2.6.0
	 *
	 * @param string $directory
	 * @param mixed $options
	 *
	 * @return string
	 */

	public static function render($directory = null, $options = null)
	{
		$output = '';

		/* command fallback */

		if (in_array($options, self::$_config['command']))
		{
			$command = $options;
		}

		/* gallery directory */

		$galleryDirectory = new Directory();
		$galleryDirectory->init($directory, self::$_config['thumbs']);
		$galleryDirectoryArray = $galleryDirectory->getArray();

		/* delete thumbs */

		if ($command == 'delete')
		{
			$galleryDirectory->remove(self::$_config['thumbs']);
		}

		/* else show thumbs */

		else
		{
			/* reverse order */

			if ($options['order'] === 'desc')
			{
				$galleryDirectoryArray = array_reverse($galleryDirectoryArray);
			}

			/* process directory */

			foreach ($galleryDirectoryArray as $key => $value)
			{
				$path = $directory . '/' . $value;
				$thumbPath = $directory . '/' . self::$_config['thumbs'] . '/' . $value;

				/* create thumbs */

				if ($command === 'create' || !file_exists($thumbPath))
				{
					self::_createThumb($value, $directory, $options);
				}
				$output .= $path;
			}
		}
		return $output;
	}

	/**
	 * createThumb
	 *
	 * @since 2.6.0
	 *
	 * @param string $fileName
	 * @param string $directory
	 * @param mixed $options
	 *
	 * @return string
	 */

	public static function _createThumb($fileName = null, $directory = null, $options = null)
	{
	}
}