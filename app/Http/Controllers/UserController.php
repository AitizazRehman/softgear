<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserCreateRequest;
use App\Contracts\UserDetailRepository;
use Exception;

class UserController extends Controller
{

    /**
     * @var [type]
     */
    protected $userDetailRepository;
    public function __construct(
        userDetailRepository $userDetailRepository
    ) {
        $this->userDetailRepository = $userDetailRepository;
    }

    public function getUsers()
    {
        try {
            $users = $this->userDetailRepository->getUsers();
            $response = [
                'details' => $users
            ];
            return response()->json($response);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
