<?php

// change the following paths if necessary
$yii=dirname(__FILE__).'/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

// Helper function to get environment variable with default value
function env($name, $default = null) {
	$value = getenv($name);
	return $value !== false ? $value : $default;
}

// YII_DEBUG: Enable debug mode based on environment variable
// Set YII_DEBUG=true or YII_DEBUG=1 to enable, default is false (production mode)
$yiiDebugEnv = env('YII_DEBUG', 'false');
$yiiDebug = filter_var($yiiDebugEnv, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
$yiiDebug = $yiiDebug !== null ? $yiiDebug : false;
defined('YII_DEBUG') or define('YII_DEBUG', $yiiDebug);

// YII_TRACE_LEVEL: Specify how many levels of call stack should be shown in each log message
// Set YII_TRACE_LEVEL=0 to disable, or higher number for more detail (default: 0 in production, 3 in development)
$yiiTraceLevelEnv = env('YII_TRACE_LEVEL', $yiiDebug ? '3' : '0');
$yiiTraceLevel = is_numeric($yiiTraceLevelEnv) ? (int)$yiiTraceLevelEnv : ($yiiDebug ? 3 : 0);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', $yiiTraceLevel);

require_once($yii);
Yii::createWebApplication($config)->run();
