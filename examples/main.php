<?php
/**
 * Project: EnvironmentProvider
 * User: MadFaill
 * Date: 06.08.14
 * Time: 21:47
 * File: main.php
 *
 */

include __DIR__."/../vendor/autoload.php";

$cfg = __DIR__.'/env/mapper.ini';

$provider = \EnvironmentProvider\Provider::initWithINIFile($cfg);
$config = $provider->Config();
$environ = $provider->Environ();

// read config
var_dump($config->get());
var_dump($config->get('group-1'));
var_dump($config->get('group-1', 'option'));
var_dump($config->get('group-1', 'option', 'g1'));

// get data from env
var_dump($environ->data('is_console'));
var_dump($environ->data('user'));