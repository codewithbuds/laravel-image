<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SignupRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'profileimage' => 'required|image|mimes:jpeg,png,jpg,gif,svg',

        ];
    }
    public function  failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'success'=>false,
            'message'=>'validation error',
            'errors'=>$validator->errors()
        ]));
    }
}
