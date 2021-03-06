<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;

class ProviderRequest extends FormRequest
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
            //
            "user_id"=>"sometimes|required",  // TODO: надо проверить sometimes
            "contacts"=>"required",
            'address'=>'required' ,
            'name'=>'required',
            'phone' => 'required|numeric|min:10',
//            'user.name' => 'sometimes|required|min:4', // вложенный request
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
