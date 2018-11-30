<?php

require('../vendor/autoload.php');

/*Константы для обычного хостинга, getend для heroku*/

$app = new Silex\Application();
$app['debug'] = true;

use FormulaParser\FormulaParser;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

$app->get('/', function() use($app) {
  return 'Hello world!';
});

$app->post('/bot', function() use($app) {
	$data = json_decode(file_get_contents('php://input'));
	if (!$data) return 'wastedFirst';
	if ($data->secret !== getenv('VK_SECRET_CODE') && $data->type !== 'confirmation') return 'wastedSecond';
	switch ($data->type) {
		case 'confirmation':
			return getenv('VK_CONFIRMATION_CODE');
			break;
		case 'message_new':
			$formula = $data->object->body;
			$precision = 2; 
			$request_params = array('user_id' => $data->object->user_id, 'message' => 'Message text', 'access_token' => getenv('VK_SECRET_TOKEN'), 'v' => '5.69');
		    $parser = new FormulaParser($formula, $precision);
		    $result = $parser->getResult();
		    $request_params['message'] = 'Ответ: '.number_format($result['1'], $precision, '.', ',');
			file_get_contents('https://api.vk.com/method/messages.send?'.http_build_query($request_params));
			return 'ok';
			break;
	}
	return 'wastedEnd';
});

$app->run();
