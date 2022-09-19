<?php

namespace Pzn\BelajarPhpMvc\Service;

use Pzn\BelajarPhpMvc\Domain\User;
use Pzn\BelajarPhpMvc\Config\Database;
use Pzn\BelajarPhpMvc\Model\UserRegisterRequest;
use Pzn\BelajarPhpMvc\Repository\UserRepository;
use Pzn\BelajarPhpMvc\Model\UserRegisterResponse;
use Pzn\BelajarPhpMvc\Exception\ValidationException;

class UserService
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(UserRegisterRequest $request): UserRegisterResponse
    {
        $this->validateUserRegistrationRequest($request);

        try {
            Database::beginTransaction();
            $user = $this->userRepository->findById($request->id);
            if ($user != null) {
                throw new ValidationException("User Id already exists");
            }
    
            $user = new User();
            $user->id = $request->id;
            $user->name = $request->name;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);
    
            $this->userRepository->save($user);
    
            $response = new UserRegisterResponse();
            $response->user = $user;

            Database::commitTransaction();
            return $response;
        }catch (\Exception $exception){
            Database::rollbackTransaction();
            throw $exception;
        }


    }

    public function validateUserRegistrationRequest(UserRegisterRequest $request)
    {
        if ($request->id == null || $request->name == null || $request->password == null ||
            trim($request->id) == "" || trim($request->name) == "" || trim($request->password) == "") {
            throw new ValidationException("Id, Name, Password can not blank");
        }
    }
}
