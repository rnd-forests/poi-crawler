<?php

namespace App\Modules\Crawler\Requests;

use App\Modules\Shared\Requests\ApiRequest;

class CreateCrawlingRequest extends ApiRequest
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
            'crawl_url' => 'required|string|url',
        ];
    }
}
