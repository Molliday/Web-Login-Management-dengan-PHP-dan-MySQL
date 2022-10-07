<?php



namespace Pzn\BelajarPhpMvc\Controller {

    require_once __DIR__ . '/../Helper/helper.php';
    
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

        public function testUpdateProfile()
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

            $this->userContoller->updateProfile();

            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[figur]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Figur]");
            
        }

        public function testPostUpdateProfileSuccess()
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

            $_POST['name'] = 'Budi';
            $this->userContoller->postUpdateProfile();

            $this->expectOutputRegex("[Location: /]");

            $result = $this->userRepository->findById("figur");
            self::assertEquals("Budi", $result->name);

        }

        public function testPostUpdateProfileValidationError()
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

            $_POST['name'] = '';
            $this->userContoller->postUpdateProfile();

            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[figur]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Id, Password can not blank]");
        }

        public function testUpdatePassword()
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

            $this->userContoller->updatePassword();

            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[figur]");
        }

        public function testPostUpdatePassword()
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

            $_POST['oldPassword'] = 'rahasia';
            $_POST['newPassword'] = 'budi';

            $this->userContoller->postUpdatePassword();

            $this->expectOutputRegex("[Location: /]");

            $result = $this->userRepository->findById($user->id);
            self::assertTrue(password_verify("budi", $result->password));
        }

        public function testPostUpdatePasswordValidationError()
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

            $_POST['oldPassword'] = '';
            $_POST['newPassword'] = '';

            $this->userContoller->postUpdatePassword();
            
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[figur]");
            $this->expectOutputRegex("[Id, Old Password, New Password can not blank]");
        }

        public function testPostUpdatePasswordWrongOldPassword()
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

            $_POST['oldPassword'] = 'salah';
            $_POST['newPassword'] = 'budi';

            $this->userContoller->postUpdatePassword();
            
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[figur]");
            $this->expectOutputRegex("[Old password is wrong]");
        }

    }
}
