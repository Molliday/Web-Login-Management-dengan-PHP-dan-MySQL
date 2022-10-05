<?php

namespace Pzn\BelajarPhpMvc\App {

    function header(string $value){
        echo $value;
    }
}

namespace Pzn\BelajarPhpMvc\Service {

    function setcookie(string $name, string $value){
        echo "$name: $value";
    }
}

namespace Pzn\BelajarPhpMvc\Controller {

    
    use PHPUnit\Framework\TestCase;
    use Pzn\BelajarPhpMvc\Config\Database;
    use Pzn\BelajarPhpMvc\Domain\Session;
    use Pzn\BelajarPhpMvc\Domain\User;
    use Pzn\BelajarPhpMvc\Repository\SessionRepository;
    use Pzn\BelajarPhpMvc\Repository\UserRepository;
    use Pzn\BelajarPhpMvc\Service\SessionService;

    class UserControllerTest extends TestCase
    {
        private UserController $userContoller;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        protected function setUp(): void
        {
            $this->userContoller = new UserController();

            $this->sessionRepository = new SessionRepository(Database::getConnection());
            $this->sessionRepository->deleteAll();

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->userRepository->deleteAll();

            putenv("mode=test");
        }

        public function testRegister()
        {
            $this->userContoller->register();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register new User]");
        }
        
        public function testPostRegisterSuccess()
        {
            $_POST['id'] = 'figur';
            $_POST['name'] = 'Figur';
            $_POST['password'] = 'rahasia';

            $this->userContoller->postRegister();

            $this->expectOutputRegex("[Location: /users/login]");
        }

        public function testPostRegisterValidationError()
        {
            $_POST['id'] = '';
            $_POST['name'] = 'Figur';
            $_POST['password'] = 'rahasia';

            $this->userContoller->postregister();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register new User]");
            $this->expectOutputRegex("[Id, Name, Password can not blank]");
        }

        public function testPostRegisterDuplicate()
        {
            $user = new User();
            $user->id = "figur";
            $user->name = "Figur";
            $user->password = "rahasia";
            
            $this->userRepository->save($user);
            
            $_POST['id'] = "figur";
            $_POST['name'] = "Figur";
            $_POST['password'] = "rahasia";

            $this->userContoller->postRegister();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register new User]");
            $this->expectOutputRegex("[User Id already exists]");

        }

        
        public function testLogin()
        {
            $this->userContoller->login();
    
            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
        }
        
        public function testLoginSuccess()
        {
            $user = new User();
            $user->id = "figur";
            $user->name = "Figur";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
    
            $this->userRepository->save($user);
    
            $_POST['id'] = 'figur';
            $_POST['password'] = 'rahasia';
    
            $this->userContoller->postLogin();
    
            $this->expectOutputRegex("[Location: /]");
            $this->expectOutputRegex("[X-PZN-SESSION: ]");
            
        }
    
        public function testLoginValidationError()
        {
            $_POST['id'] = '';
            $_POST['password'] = '';
    
            $this->userContoller->postLogin();
    
            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id, Password can not blank]");
        }
    
        public function testLoginUserNotFound()
        {
            $_POST['id'] = 'notfound';
            $_POST['password'] = 'notfound';
    
            $this->userContoller->postLogin();
    
            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id or password is wrong]");
        }
    
        public function testLoginWrongPassword()
        {
            $user = new User();
            $user->id = "figur";
            $user->name = "Figur";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
    
            $this->userRepository->save($user);
    
            $_POST['id'] = 'figur';
            $_POST['password'] = 'salah';
    
            $this->userContoller->postLogin();
    
            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id or password is wrong]");
        }

        public function testLogout()
        {
            $user = new User();
            $user->id = "figur";
            $user->name = "Figur";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userContoller->logout();

            $this->expectOutputRegex("[Location: /]");
            $this->expectOutputRegex("[X-PZN-SESSION: ]");
        }

    }
}
