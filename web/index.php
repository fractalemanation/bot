<?php

require('../vendor/autoload.php');

/*Константы для обычного хостинга, getend для heroku*/

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

$app->get('/', function() use($app) {
  return 'Hello world!';
});

$app->post('/bot', function() use($app) {
	$data = json_encode(file_get_contents('php://input'));
	if (!$data) return 'wasted';
	if ($data->secret !== getenv('VK_SECRET_TOKEN') && $data->type !== 'confirmation') return 'wasted';
	switch ($data->type) {
		case 'confirmation':
			return getenv('VK_CONFIGURATION_CODE');
			break;
		case 'message_new':
			# code...
			break;
	}
	return 'wasted';
});

$app->run();
