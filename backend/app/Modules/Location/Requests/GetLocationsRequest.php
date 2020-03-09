<?php

namespace App\Modules\Location\Requests;

use App\Modules\Shared\Requests\ApiRequest;

class GetLocationsRequest extends ApiRequest
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
            'name' => 'nullable|string',
            'created_at' => 'nullable|date_format:"Y-m-d"',
            'user_id' => 'required_with:created_at',
            'province_id' => 'required_with:district_id',
            'coordinates' => ['nullable', 'regex:"^\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?),[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)$"'],
//            'longitude' => ['nullable', 'regex:"^\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$"'],
//            'latitude' => ['required_with:longitude', 'regex:"^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)$"'],
        ];
    }
}
