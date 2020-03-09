<?php

namespace App\Modules\Location\Requests;

use App\Modules\Shared\Requests\ApiRequest;

class SearchLocationRequest extends ApiRequest
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
//            'name' => 'nullable|string',
//            'coordinates' => ['nullable', 'regex:"^\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?),[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)$"'],
//            'type_ids' => 'required_with:coordinates',
            'longitude' => ['nullable', 'regex:"^\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$"'],
            'latitude' => ['required_with:longitude', 'regex:"^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)$"'],
//            'groups' => ['required_with:longitude'],
        ];
    }
}
