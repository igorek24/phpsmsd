<?php declare(strict_types=1);

use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Slim\App;


require __DIR__ . '/../init.php';
require APP_ROOT . 'src/libs/autoload.php';
$containerBuilder = new ContainerBuilder();
// Set up settings
$containerBuilder->addDefinitions(APP_ROOT . 'src/container.php');

// Load .env file
try {
    if (file_exists(APP_ROOT . '.dev.env')) {
        Dotenv::createImmutable(APP_ROOT, '.dev.env')->load();
    } else {
        Dotenv::createImmutable(APP_ROOT)->load();
    }
} catch (Exception $e) {
    echo $e->getMessage();
    exit();
}

header('X-Powered-By: ' . $_ENV['APP_HEATHER_X_POWERED_BY']);
header("Access-Control-Allow-Origin: " . $_ENV['APP_HEATHER_ACC_CT_ALLOW_ORIGIN']);
ini_set('session.cookie_httponly', 1);
if (!$_ENV['APP_DEV_MODE']) {
    ini_set('session.cookie_secure', 1);
}
ini_set('session.cookie_samesite', 'lax');

session_name($_ENV['APP_SESSION_NAME']);

session_start();

// Build PHP-DI Container instance
$container = $containerBuilder->build();


// Create App instance
$app = $container->get(App::class);

// Register routes
foreach (glob(APP_SRC_ROOT . "routes/*_routes.php") as $routeFilename) {
    (require $routeFilename)($app);
}


// Register middleware
(require APP_SRC_ROOT . 'middleware.php')($app);


return $app;