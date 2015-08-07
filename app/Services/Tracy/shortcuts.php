<?php


if (!function_exists('d')) {
	/**
	 * Tracy\Debugger::dump() shortcut.
	 * @tracySkipLocation
	 */
	function d($var,$return = false)
	{
		\Tracy\Debugger::dump($var,$return);
	}
}

