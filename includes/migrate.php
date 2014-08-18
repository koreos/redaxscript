<?php

/**
 * breadcrumb
 *
 * @since 2.1.0
 * @deprecated 2.0.0
 *
 * @package Redaxscript
 * @category Migrate
 * @author Gary Aylward
 */

function breadcrumb()
{
	$options = array(
		'className' => array(
			'list' => 'list_breadcrumb',
			'divider' => 'divider'
		)
	);
	$breadcrumb = new Redaxscript\Breadcrumb(Redaxscript\Registry::getInstance(), Redaxscript\Language::getInstance(), $options);
	echo $breadcrumb->render();
}

/**
 * helper class
 *
 * @since 2.1.0
 * @deprecated 2.0.0
 *
 * @package Redaxscript
 * @category Migrate
 * @author Kim Kha Nguyen
 */

function helper_class()
{
	$helper = new Redaxscript\Helper(Redaxscript\Registry::getInstance());
	echo $helper->getClass();
}

/**
 * helper subset
 *
 * @since 2.1.0
 * @deprecated 2.0.0
 *
 * @package Redaxscript
 * @category Migrate
 * @author Kim Kha Nguyen
 */

function helper_subset()
{
	$helper = new Redaxscript\Helper(Redaxscript\Registry::getInstance());
	echo $helper->getSubset();
}

/**
 * language shortcut
 *
 * @since 2.2.0
 * @deprecated 2.0.0
 *
 * @package Redaxscript
 * @category Migrate
 * @author Henry Ruhs
 *
 * @param string $key
 * @param string $index
 *
 * @return string
 */

function l($key = null, $index = null)
{
	$language = Redaxscript\Language::getInstance();
	$output = $language->get($key, $index);
	return $output;
}

/**
 * migrate constants
 *
 * @since 2.1.0
 * @deprecated 2.0.0
 *
 * @package Redaxscript
 * @category Migrate
 * @author Henry Ruhs
 * @author Gary Aylward
 *
 * @return array
 */

function migrate_constants()
{
	/* get user constants */

	$constants = get_defined_constants(true);
	$constants_user = $constants['user'];

	/* process constants user */

	foreach ($constants_user as $key => $value)
	{
		/* transform to camelcase */

		$key = mb_convert_case($key, MB_CASE_TITLE);
		$key[0] = strtolower($key[0]);

		/* remove underline */

		$key = str_replace('_', '', $key);

		/* store in array */

		$output[$key] = $value;
	}
	return $output;
}