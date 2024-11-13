<?php declare(strict_types=1);

use App\Factory\LoggerFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Middleware\ErrorMiddleware;
use Slim\Flash\Messages;
use Symfony\Component\Console\Application;

return [
    App::class => function (ContainerInterface $container) {
        AppFactory::setContainer($container);
        return AppFactory::create();
    },

    ResponseFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getResponseFactory();
    },
    LoggerFactory::class => function () {
        $conf = [
            'name' => $_ENV['LOGGER_NAME'],
            'path' => APP_ROOT . $_ENV['LOGGER_PATH'],
            'filename' => $_ENV['LOGGER_FILENAME'],
            'level' => filter_var($_ENV['APP_DEV_MODE'], FILTER_VALIDATE_BOOLEAN) ? 100 : (int)$_ENV['LOGGER_LEVEL'],
            'test' => filter_var($_ENV['LOGGER_TEST'], FILTER_VALIDATE_BOOLEAN),
        ];
        return new LoggerFactory($conf);
    },
    ErrorMiddleware::class => function (ContainerInterface $container) {
        $app = $container->get(App::class);
        $loggerFactory = $container->get(LoggerFactory::class);
        $logger = $loggerFactory->addFileHandler('app_error.log')->createLogger();
        if (filter_var($_ENV['APP_DEV_MODE'], FILTER_VALIDATE_BOOLEAN)) {
            return new ErrorMiddleware(
                $app->getCallableResolver(),
                $app->getResponseFactory(),
                true, true, true,
                $logger
            );
        } else {
            return new ErrorMiddleware(
                $app->getCallableResolver(),
                $app->getResponseFactory(),
                filter_var($_ENV['SLIM_DISPLAY_ERR_DETAILS'], FILTER_VALIDATE_BOOLEAN),
                filter_var($_ENV['SLIM_LOG_ERRS'], FILTER_VALIDATE_BOOLEAN),
                filter_var($_ENV['SLIM_LOG_ERR_DETAILS'], FILTER_VALIDATE_BOOLEAN),
                $logger
            );
        }
    },
    MysqliDb::class => function () {
        $db = new MysqliDb(
            [
                'host' => $_ENV['MYSQL_HOST'],
                'username' => $_ENV['MYSQL_USER'],
                'password' => $_ENV['MYSQL_PASSWORD'],
                'db' => $_ENV['MYSQL_SCHEMA'],
                'port' => $_ENV['MYSQL_PORT'],
                'prefix' => $_ENV['MYSQL_PREFIX'],
                'charset' => $_ENV['MYSQL_CHARSET']
            ]
        );
        try {
            $db->ping();
        } catch (Exception $e) {
            echo "Database error: " . $e->getMessage();
            exit();
        }
        return $db;
    },
    Messages::class => function () {
        return new Messages();
    },
    Application::class => function (ContainerInterface $container) {
        $application = new Application();
        foreach (require(APP_SRC_ROOT . 'console_comands.php') as $class) {
            $application->add($container->get($class));
        }
        return $application;
    },
];