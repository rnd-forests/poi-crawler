<?php

namespace App\Modules\Location\Requests;

use App\Modules\Shared\Requests\ApiRequest;

class CreateLocationRequest extends ApiRequest
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
            'description' => 'string',
            'avatar' => 'string',
            'longitude' => 'numeric',
            'latitude' => 'numeric',
            'weight' => 'integer|min:1|max:4',
            'source' => 'string',
            'type' => 'required|array',
            'type.*._id' => 'distinct',
            'keywords' => 'string',
            'price_range' => 'string',
            'area.district' => 'required',
            'area.province' => 'required',
//            'area.ward' => 'required',
            'geometry.coordinates' => 'required',
        ];
    }
}
