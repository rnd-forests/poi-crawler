<?php
/**
 * Created by Trang Ha Viet.
 * Contact: <viettrangha@gmail.com>
 * 04/10/2018 15:21
 */

namespace App\Modules\User\Services;

use App\Exceptions\NotFoundException;
use App\Modules\User\Models\User;
use Illuminate\Http\Request;

class UserService
{
    protected $creationFields = [
        'email',
        'password',
        'name',
        'phone',
        'role_id',
    ];

    public function createUser(Request $request)
    {
        return User::create($request->all($this->creationFields));
    }

    public function getUser($id)
    {
        return User::where('_id', '=', $id)->first();
    }

    public function updateUser($id, $request)
    {
        return User::where('_id', '=', $id)
            ->update($request->all(array_merge(
                $this->creationFields,
                ['status', $request->status]
            )));
    }

    /**
     * @param $id
     * @return mixed
     * @throws NotFoundException
     */
    public function firstOrFail($id)
    {
        $user = User::where('_id', '=', $id)->first();

        if ($user) return $user;

        throw new NotFoundException();
    }

    /**
     * @param $id
     * @return bool|null
     * @throws \Exception
     */
    public function delete($id)
    {
        return User::where('_id', $id)->delete();
    }
}
