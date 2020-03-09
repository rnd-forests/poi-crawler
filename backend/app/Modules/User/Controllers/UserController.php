<?php

namespace App\Modules\User\Controllers;

use App\Exceptions\NotFoundException;
use App\Modules\User\Requests\CreateUserRequest;
use App\Modules\Shared\Controllers\Controller;
use App\Modules\User\Models\User;
use App\Modules\User\Requests\UpdateUserRequest;
use App\Modules\User\Resources\UserCollection;
use App\Modules\User\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $service;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserService $userService)
    {
        $this->service = $userService;
    }

    /**
     * Show the application dashboard.
     *
     * @param Request $request
     * @return UserCollection|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        if ($request->has('page')) {
            return new UserCollection(User::paginate($request->query('per_page', 15)));
        }

        return $this->resSuccess(User::all());
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws NotFoundException
     */
    public function show($id)
    {
        return $this->resSuccess($this->service->firstOrFail($id));
    }

    public function store(CreateUserRequest $request)
    {
        return $this->resSuccess($this->service->createUser($request));
    }

    /**
     * @param $id
     * @param UpdateUserRequest $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws NotFoundException
     */
    public function update($id, UpdateUserRequest $request)
    {
        $isSuccess = $this->service->updateUser($id, $request);

        if ($isSuccess) {
            return $this->resSuccess();
        }

        throw new NotFoundException();
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function delete($id)
    {
        if ($this->service->delete($id)) {
            return $this->resNoContent();
        }

        return $this->resBadRequest();
    }
}
