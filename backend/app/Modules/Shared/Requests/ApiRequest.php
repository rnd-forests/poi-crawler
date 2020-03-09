<?php

namespace App\Modules\Shared\Requests;

use App\Exceptions\ValidationException;
use Illuminate\Validation\ValidationException as LaravelValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

abstract class ApiRequest extends LaravelFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    abstract public function rules();

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    abstract public function authorize();

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = (new LaravelValidationException($validator))->errors();

        $message = implode(' ', array_map(function ($e) {
            return $e[0];
        }, $errors));

        throw new ValidationException($errors, $message);
    }
}
