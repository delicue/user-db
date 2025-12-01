<?php 

namespace App\Controllers;

use App\Database;
use App\Forms\AddUserForm;
use App\Http\Route;
use App\Log;
use App\Session;
use App\View;
use Exception;
use NoDiscard;

class HomeController extends Controller {

    #[Route('/', 'GET')]
    public function __invoke(): string  {
        $users = Database::getInstance()->fetchAll("SELECT * FROM users");
        $usersCount = count($users);
        return View::render('pages/index.view', ['users' => $users, 'usersCount' => $usersCount, 'title' => 'Home Page']);
    }

    #[NoDiscard('The return value should not be discarded')]
    #[Route('/get-users', 'GET')]
    public function getUsers(): string {
        $db = Database::getInstance();
        $users = $db->fetchAll("SELECT * FROM users");
        return json_encode($users);
    }

    #[Route('/add-user', 'POST')]
    public function addUser(): string {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method.');
            }
            if (!verifyCsrfToken('add_user')) {
                throw new Exception('Invalid CSRF token.');
            }
            
            $name = $_POST['name'];
            $email = $_POST['email'];

            $db = Database::getInstance();
            
            $users = Database::getInstance()->fetchAll("SELECT * FROM users");
            $userCount = count($users);
            Log::info("Total users in database: " . $userCount);

            if (AddUserForm::validate(['name' => $name, 'email' => $email, 'user_limit' => $userCount])) {
                $stmt = $db->getConnection()->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);
                $stmt->execute();
            }
            else {
+                // Store validation errors in session before redirect
                Session::set('form_errors', AddUserForm::getErrors());
                throw new Exception('Validation failed: ' . implode(', ', AddUserForm::$errors));
            }
        }
        catch (Exception $e) {
            http_response_code(400);
            return "Error: " . htmlspecialchars($e->getMessage());
        }
        finally {
            header('Location: /');
            exit();
        }
    }
}