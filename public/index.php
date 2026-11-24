<?php
// Point d'entree unique de l'application

declare(strict_types=1);

define('ROOT', dirname(__DIR__));
define('VIEWS', ROOT . '/src/Views');
define('UPLOAD_DIR', __DIR__ . '/assets/uploads');

// Autoloader : App\Core\Router -> src/Core/Router.php
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = ROOT . '/src/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require ROOT . '/src/helpers.php';

use App\Core\Router;
use App\Core\Session;
use App\Models\UserModel;

Session::start();

// On recharge l'utilisateur connecte pour tenir compte d'un changement de role ou d'un bannissement
$sessionUser = Session::user();
if ($sessionUser !== null) {
    try {
        $fresh = (new UserModel())->findById((int) $sessionUser['id']);
    } catch (PDOException $e) {
        $fresh = null;
    }
    if ($fresh === null || (int) $fresh['is_banned'] === 1) {
        Session::logout();
        Session::start();
    } else {
        Session::refresh($fresh);
    }
}

$router = new Router();
require ROOT . '/src/routes.php';

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
