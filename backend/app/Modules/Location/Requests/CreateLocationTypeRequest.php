<?php

namespace App\Modules\Location\Requests;

use App\Modules\Shared\Requests\ApiRequest;

class CreateLocationTypeRequest extends ApiRequest
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
            'slug' => 'required|string',
            'description' => 'nullable|string',
        ];
    }
}
