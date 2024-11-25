<?php

/*if((isset($_SERVER["ENV"]) && $_SERVER["ENV"]!=="production") || isset($_GET["dbg"])) {
// comment out the following two lines when deployed to production
    defined('YII_DEBUG') or define('YII_DEBUG', true);
    defined('YII_ENV') or define('YII_ENV', 'dev');
}else{
    defined('YII_DEBUG') or define('YII_DEBUG', false);
    defined('YII_ENV') or define('YII_ENV', 'prod');
}
const APP_KEY = "FBB_FRONT";

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/web.php');

(new yii\web\Application($config))->run();



*/






require __DIR__ . '/../bootstrap/autoload.php';
/*
 * --------------------------------------------------------------------------
 * Initialize SideKit library
 * --------------------------------------------------------------------------
 *
 * This step is required *prior* adding the application script.
 *
 */
require __DIR__ . '/../bootstrap/sidekit.php';
/*
 * --------------------------------------------------------------------------
 * Initialize custom aliases
 * --------------------------------------------------------------------------
 *
 * Add custom aliases to the application. Added after sidekit to take
 * advantage of its loaded configuration values
 */
require __DIR__ . '/../bootstrap/aliases.php';
/*
 * --------------------------------------------------------------------------
 * Configure and Go!
 * --------------------------------------------------------------------------
 *
 * Bootstrap the configuration processes and get and Application ready to use.
 * Applying configuration details in a different file allow us to free up
 * unnecessary code on the entry script.
 */
$app = require __DIR__ . '/../bootstrap/web.php';
/* @var $app \yii\web\Application */

$app->run();

