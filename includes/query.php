<?php

/* database connect */

function database_connect($host = '', $name = '', $user = '', $password = '')
{
	$database_connect = mysql_connect($host, $user, $password);
	$database_select = mysql_select_db($name);
	if ($database_connect && $database_select)
	{
		$query = 'SET NAMES \'utf8\'';
		mysql_query($query);
		$_SESSION[ROOT . '/db_connected'] = 1;
	}
	else
	{
		$_SESSION[ROOT . '/db_connected'] = 0;
		$_SESSION[ROOT . '/logged_in'] = '';
	}
}

/* shortcut */

function s($name = '')
{
	static $settings;

	/* query settings */

	if ($settings == '')
	{
		$query = 'SELECT name, value FROM ' . PREFIX . 'settings';
		$result = mysql_query($query);
		if ($result)
		{
			while ($r = mysql_fetch_assoc($result))
			{
				$settings[$r['name']] = $r['value'];
			}
		}
	}
	$output = $settings[$name];

	/* charset fallback */

	if (DB_CONNECTED == 0 && $name == 'charset')
	{
		$output = 'utf-8';
	}
	return $output;
}

/* retrieve */

function retrieve($column = '', $table = '', $field = '', $value = '')
{
	static $retrieve;

	/* fetch from cache */

	if ($retrieve[$column . $table . $field . $value])
	{
		$output = $retrieve[$column . $table . $field . $value];
	}

	/* else query */

	else if ($column && $table && $field && $value)
	{
		$query = 'SELECT ' . $column . ' FROM ' . PREFIX . $table . ' WHERE ' . $field . ' = \'' . $value . '\'';
		$result = mysql_query($query);
		if ($result)
		{
			$r = mysql_fetch_assoc($result);
			$output = $retrieve[$column . $table . $field . $value] = $r[$column];
		}
	}
	return $output;
}

/* query table */

function query_table($input = '')
{
	static $table;

	/* fetch from cache */

	if ($table[$input])
	{
		$output = $table[$input];
	}

	/* else query */

	else
	{
		$category = retrieve('id', 'categories', 'alias', $input);
		if ($category)
		{
			$output = $table[$input] = 'categories';
		}
		else
		{
			$article = retrieve('id', 'articles', 'alias', $input);
			if ($article)
			{
				$output = $table[$input] = 'articles';
			}
		}
	}
	return $output;
}

/* query plumb */

function query_plumb($column = '', $table = '', $function = '')
{
	if ($column && $table && $function)
	{
		$query = 'SELECT ' . $function . '(' . $column . ') FROM ' . PREFIX . $table;
		$result = mysql_query($query);
		if ($result)
		{
			$output = mysql_result($result, 0, 0);
		}
	}
	return $output;
}

/* query total */

function query_total($table = '', $field = '', $value = '')
{
	$query = 'SELECT id FROM ' . PREFIX . $table;
	if ($field)
	{
		$query .= ' WHERE ' . $field . ' = \'' . $value . '\'';
	}
	$result = mysql_query($query);
	if ($result)
	{
		$output = mysql_num_rows($result);
	}
	return $output;
}

/* build string */

function build_string($table = '', $id = '')
{
	static $string;

	/* fetch from cache */

	if ($string[$table . $id])
	{
		$output = $string[$table . $id];
	}

	/* else query */

	else if ($table && $id)
	{
		$query = 'SELECT p.alias, c.alias';
		if ($table != 'categories')
		{
			$query .= ', a.alias';
		}
		$query .= ' FROM ' . PREFIX . $table . ' AS';

		/* switch table */

		switch ($table)
		{
			case 'categories':
				$query .= ' c LEFT JOIN ' . PREFIX . 'categories AS p ON c.parent = p.id WHERE c.id = ' . $id;
				break;
			case 'articles':
				$query .= ' a LEFT JOIN ' . PREFIX . 'categories AS c ON a.category = c.id LEFT JOIN ' . PREFIX . 'categories AS p ON c.parent = p.id WHERE a.id = ' . $id;
				break;
			case 'comments':
				$query .= ' m LEFT JOIN ' . PREFIX . 'articles AS a ON m.article = a.id LEFT JOIN ' . PREFIX . 'categories AS c ON a.category = c.id LEFT JOIN ' . PREFIX . 'categories AS p ON c.parent = p.id WHERE m.id = ' . $id;
				break;
		}
		$result = mysql_query($query);

		/* collect output */

		if ($result)
		{
			$output = mysql_fetch_row($result);
		}
		if (is_array($output))
		{
			$output = array_filter($output);
			$output = implode('/', $output);
		}

		/* comment id */

		if ($table == 'comments' && $output)
		{
			$output .= '#comment-' . $id;
		}

		/* store in cache */

		if ($output)
		{
			$string[$table . $id] = $output;
		}
	}
	return $output;
}

/* future update */

function future_update($table = '')
{
	$general_update_query = 'UPDATE ' . PREFIX . $table . ' SET status = 1 WHERE date < \'' . NOW . '\' && status = 2';
	if ($table == 'articles')
	{
		$general_select_query = 'SELECT id, date FROM ' . PREFIX . 'articles WHERE date < \'' . NOW . '\' && status = 2';
		$general_result = mysql_query($general_select_query);
		if ($general_result)
		{
			while ($r = mysql_fetch_assoc($general_result))
			{
				$comments_update_query = 'UPDATE ' . PREFIX . 'comments SET date = \'' . $r['date'] . '\', status = 1 WHERE article = ' . $r['id'] . ' && status = 2';
				mysql_query($comments_update_query);
			}
		}
	}
	mysql_query($general_update_query);
}
?>