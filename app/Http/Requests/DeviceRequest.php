<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class DeviceRequest extends FormRequest
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
            "alias"=>"required",
            "imei"=>"required",
            'name'=>'required',
        ];
    }


    /**
     * @return array
     */
    public function messages()
    {
        $validation = [];
        return array_merge($validation,parent::messages()); // TODO: Change the autogenerated stub
    }

    /**
     * Оформление для JSON ответа 
     * 
     * @param Validator $validator
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->messages(),
            'errorType'=>'VALIDATION_ERROR'
        ],422));
    }

}
