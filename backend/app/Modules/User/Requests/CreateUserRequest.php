<?php

namespace App\Modules\User\Requests;

use App\Modules\Shared\Requests\ApiRequest;

class CreateUserRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'role_id' => 'required|numeric|in:' . implode(',', array_values(config('constants.USER.ROLE'))),
            'password' => 'required|string|min:6|max:128',
            'phone' => 'string|min:10|max:20',
        ];
    }
}
