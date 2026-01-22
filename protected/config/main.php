<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
Yii::setPathOfAlias('chartjs', dirname(__FILE__) . '/../extensions/yii-chartjs');

/**
 * Helper function to get environment variable or return default value
 * @param string $name Environment variable name
 * @param mixed $default Default value if ENV variable doesn't exist
 * @return mixed Environment variable value or default
 */
function env($name, $default = null)
{
	$value = getenv($name);
	return !empty($value) ? $value : $default;
}

// Database configuration with environment variable support
// Environment variables override default values if they exist
$dbHost = env('DB_HOST', '127.0.0.1');
$dbName = env('DB_NAME', 'dashboard');
$dbUser = env('DB_USER', 'mode');
$dbPassword = env('DB_PASSWORD', '');
$dbCharset = env('DB_CHARSET', 'utf8');
$dbPort = env('DB_PORT', null);

// Build connection string
$dbConnectionString = 'mysql:host=' . $dbHost;
if ($dbPort !== null) {
	$dbConnectionString .= ';port=' . $dbPort;
}
$dbConnectionString .= ';dbname=' . $dbName;

// Application parameters with environment variable support
$appSalt = env('APP_SALT', 'phubeThAspADReDRuRatreprEwUba2Hu');
$appTimezone = env('APP_TIMEZONE', 'Asia/Jakarta');
$appTimeout = env('APP_TIMEOUT', 900);
$appAdminEmail = env('APP_ADMIN_EMAIL', 'webmaster@example.com');
$appCompanyName = env('APP_COMPANY_NAME', 'Mode Fashion Group');
$appPaginationSize = env('APP_PAGINATION_SIZE', '10');

// Convert timeout to integer if it's a string
$appTimeout = is_numeric($appTimeout) ? (int)$appTimeout : 900;
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'name' => 'Dashboard v2',
	'language' => 'id',
	//	'theme'=> 'ace',

	// preloading 'log' component
	'preload' => array('log', 'chartjs'),

	// autoloading model and component classes
	'import' => array(
		'application.models.*',
		'application.components.*',
	),

	'modules' => array(
		// uncomment the following to enable the Gii tool

		'gii' => array(
			'class' => 'system.gii.GiiModule',
			'password' => '123456',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters' => array('127.0.0.1', '::1'),
		),

	),

	// application components
	'components' => array(
		'user' => array(
			// enable cookie-based authentication
			'allowAutoLogin' => true,
		),
		// uncomment the following to enable URLs in path-format

		'urlManager' => array(
			'urlFormat' => 'path',
			'rules' => array(
				'<controller:\w+>/<id:\d+>' => '<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
				'<controller:\w+>/<action:\w+>' => '<controller>/<action>',
			),
		),

		// Database configuration - values can be overridden by environment variables
		'db' => array(
			'connectionString' => $dbConnectionString,
			'emulatePrepare' => true,
			'username' => $dbUser,
			'password' => $dbPassword,
			'charset' => $dbCharset,
		),
		'authManager' => array(
			'class' => 'CDbAuthManager',
			'connectionID' => 'db',
			'itemTable' => 'authitem',
			'itemChildTable' => 'authitemchild',
			'assignmentTable' => 'authassignment'
		),

		'errorHandler' => array(
			// use 'site/error' action to display errors
			'errorAction' => 'site/error',
		),
		'log' => array(
			'class' => 'CLogRouter',
			'routes' => array(
				array(
					'class' => 'CFileLogRoute',
					'levels' => 'error, warning, info',
				),
				// uncomment the following to show log messages on web pages

				//				array(
				//					'class'=>'CWebLogRoute',
				//				),

			),
		),
		//component for creating chart
		'chartjs' => array('class' => 'chartjs.components.ChartJs'),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	// Values can be overridden by environment variables
	'params' => array(
		'company' => array(
			'name' => $appCompanyName
		),
		// this is used in contact page
		'adminEmail' => $appAdminEmail,
		'salt' => $appSalt,
		'timezone' => $appTimezone,
		'timeout' => $appTimeout,
		'pagination' => array(
			'size' => $appPaginationSize
		)
	),
);
