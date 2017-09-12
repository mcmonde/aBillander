<?php

/*
|--------------------------------------------------------------------------
| Helper functions.
|--------------------------------------------------------------------------
|
| ToDo: move to a proper location if necessary; Google this: 
| "laravel 5 where to put helpers".
|
*/

function l($string = NULL, $data = [], $langfile = NULL)
	{
        if ($langfile == NULL) $langfile = \App\Context::getContext()->controller;

        if (Lang::has($langfile.'.'.$string))
			return Lang::get($langfile.'.'.$string, $data);
	//	elseif (Lang::has('_allcontrollers.'.$string))
	//		return Lang::get('_allcontrollers.'.$string);
		else 
		{
			foreach ($data as $key => $value)
			{
				$string = str_replace(':'.$key, $value, $string);
			}
			return $string;
		}
	}


function abi_r($foo, $exit=false)
	{
		echo '<pre>';
		print_r($foo);
		echo '</pre>';

		if ($exit) die();
	}


/**
 * PHP Multi Dimensional Array Combinations.
 *
 * 
 */
function combos($data, &$all = array(), $group = array(), $val = null, $i = 0)
{
	if (isset($val))
	{
		array_push($group, $val);
	}

	if ($i >= count($data))
	{
		array_push($all, $group);
	}
	else
	{
		foreach ($data[$i] as $v)
		{
			combos($data, $all, $group, $v, $i + 1);
		}
	}

	return $all;
}

/**
 * https://gist.github.com/aphoe/26495499a014cd8daf9ac7363aa3b5bd
 * @param $route
 * @return bool
 */
function checkRoute($route='') {

	if ($route=='/') return true;

	$route=ltrim($route, '/');

    $routes = \Route::getRoutes()->getRoutes();
    foreach($routes as $r){
//        if($r->getUri() == $route){
        if($r->uri() == $route){
            return true;
        }
    }

    return false;
}