<?php

require_once __DIR__ . '/model/database.php'; //nchargi connexion a la data base 
require_once __DIR__ . '/controller/AuthController.php';// controleur d'authentification

// route via ?route=...
$route = $_GET['route'] ?? 'register';//kani mawjuda URL la page s'ouvre snn par defaut register
$auth = new AuthController();//pour gerer les pages

switch ($route) {

    case 'register':
        $auth->register(); 
        break;

    case 'login':
        $auth->login();
        break;

    case 'client':
        require __DIR__ . '/view/frontoffice/client/client_home.php';
        break;


    case 'freelancer':
    require __DIR__ . '/view/frontoffice/freelancer/freelancer_home.php';
    break;

    case 'admin':
    header("Location: index.php?route=backoffice");
    exit();

    case 'create_project':
        require __DIR__ . '/view/create_project.php';
        break;

    case 'profil':
    require __DIR__ . '/controller/PortfolioController.php';
    $controller = new PortfolioController();
    $controller->show();
    break;


    case 'feedback':
        require __DIR__ . '/view/feedback.php';
        break;

    case 'blog':
        require __DIR__ . '/view/blog.php';
        break;

    case 'reclamation':
        require __DIR__ . '/view/reclamation.php';
        break;

    case 'logout':
    $auth->logout();   
    break;


case 'create_portfolio':
    require __DIR__ . '/controller/PortfolioController.php';
    $controller = new PortfolioController();
    $controller->create();
    break;

case 'backoffice':
    require __DIR__ . '/controller/BackofficeController.php';
    $c = new BackofficeController();
    $c->index();
    break;

case 'delete_user':
    require __DIR__ . '/controller/BackofficeController.php';
    $c = new BackofficeController();
    $c->deleteUser();
    break;

    
case 'update_portfolio':
    require __DIR__ . '/controller/PortfolioController.php';
    $controller = new PortfolioController();
    $controller->update();  // 👉 tu dois APPELER update()
    break;



case 'ban_user':
    require __DIR__ . '/controller/BackofficeController.php';
    $c = new BackofficeController();
    $c->banUser();
    break;

 

case 'forgot_password':
    require __DIR__ . '/view/frontoffice/forgot_password.php';
    break;

case 'send_reset':
    require __DIR__ . '/controller/ResetPasswordController.php';
    $controller = new ResetPasswordController();
    $controller->sendResetLink();
    break;

case 'reset_password':
    require __DIR__ . '/view/frontoffice/reset_password.php';
    break;

case 'update_password':
    require __DIR__ . '/controller/ResetPasswordController.php';
    $controller = new ResetPasswordController();
    $controller->updatePassword();
    break;


case 'contact':
    require __DIR__ . '/view/frontoffice/contact.php';
    break;


case 'view_ban':
    require __DIR__ . '/controller/BackofficeController.php';
    $c = new BackofficeController();
    $c->viewBan();
    break;




    default:
        http_response_code(404);
        echo "Page non trouvée";
}
