<?php
require_once __DIR__ . '/../model/User.php';

class BackofficeController {

    public function index() {

        

        if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Admin') {
            header("Location: index.php?route=login");
            exit();
        }

        $model = new User();
        $users = $model->getAllUsers();

        include __DIR__ . '/../view/backoffice/dashboard.php';
    }

    public function deleteUser() {

        

        if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Admin') {
            header("Location: index.php?route=login");
            exit();
        }

        if (isset($_POST['id'])) {
            $model = new User();
            $model->deleteUser($_POST['id']);
        }

        header("Location: index.php?route=backoffice");
        exit();
    }


    public function banUser() {
    if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Admin') {
        header("Location: index.php?route=login");
        exit();
    }

    if (isset($_POST['id']) && isset($_POST['status'])) {
        $model = new User();
        $model->setBanStatus($_POST['id'], $_POST['status']);
    }

    header("Location: index.php?route=backoffice");
    exit();
}


}
