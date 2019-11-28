<?php
use Symfony\Component\VarDumper\VarDumper;

/**
 * Dump variable
 * 
 * @codeCoverageIgnore
 * @return mixed
 */
function dump()
{
    if (function_exists('xdebug_var_dump') === true) {
        foreach (func_get_args() as $variable) {
            xdebug_var_dump($variable);
        }

        return;
    }

    foreach (func_get_args() as $variable) {
        VarDumper::dump($variable);
    }
}

/**
 * Starts with
 * 
 * @param string $condition
 * @param string $from
 * @param int $length
 * @param bool $ignore_whitespaces
 * @return bool
 */
function startsWith($condition, $from, $length = 1, $ignore_whitespaces = false)
{
    if ($ignore_whitespaces === true) {
        $from = ltrim($from);
    }

    if (substr($from, 0, $length) === $condition) {
        return true;
    }

    return false;
}

/**
 * Starts with
 * 
 * @param string $condition
 * @param string $from
 * @param int $length
 * @param bool $ignore_whitespaces
 * @return bool
 */
function endsWith($condition, $from, $length = 1, $ignore_whitespaces = false)
{
    if ($ignore_whitespaces === true) {
        $from = rtrim($from);
    }

    if (substr($from, -$length) === $condition) {
        return true;
    }

    return false;
}

/**
 * Underscore to StudlyCaps
 * 
 * @param string $text 
 * @return string
 */
function underscoreToStudlyCaps($text)
{
    return str_replace(' ', '', ucwords(str_replace('_', ' ', $text)));
}
