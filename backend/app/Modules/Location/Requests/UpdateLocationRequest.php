<?php

namespace App\Modules\Location\Requests;

use App\Modules\Shared\Requests\ApiRequest;

class UpdateLocationRequest extends ApiRequest
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
            'slug' => 'required|string', // check unique at controller or service to reduce no. of query
            'description' => 'nullable|string',
            'avatar' => 'nullable|string',
            'longitude' => 'numeric',
            'latitude' => 'numeric',
            'weight' => 'nullable|integer|min:1|max:4',
//            'source' => 'string',
//            'source_url' => 'string',
            'type' => 'required|array',
            'type.*._id' => 'distinct',
//            'keywords' => 'nullable|string',
            'formatted_address' => 'nullable|string',
//            'price_range' => 'string',
            'area.district' => 'required',
            'area.province' => 'required',
            'geometry.coordinates' => 'required',
        ];
    }
}
