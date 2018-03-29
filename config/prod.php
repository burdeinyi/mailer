<?php

/**
 * @var Silex\Application $app
 */

$parameters =\Symfony\Component\Yaml\Yaml::parse(file_get_contents(__DIR__ . '/parameters.yml'))['parameters'];

$app->register(new Sorien\Provider\PimpleDumpProvider());
$app->register(new Silex\Provider\SwiftmailerServiceProvider());
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new \Silex\Provider\MonologServiceProvider(), array(
	'monolog.logfile' => (bool) $parameters['debug'] ? __DIR__ . '/../var/logs/development.log' : 'php://stderr',
));

$app['email_config'] = $parameters;
