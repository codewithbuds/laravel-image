<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UserRequest extends FormRequest
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
        
        return match($this->method()){
            'POST' => $this->Registration(),
            'POST' =>$this->Login(),
            'POST' =>$this->ForgetPassword(),
            'POST' =>$this->ResetPassword(),
        };
    }
        //validation for postRegistration
    public function Registration(): array 
        {
            return [
                'name' =>' required|max:255',
                'email' =>' required|email|unique:users',
                'password' => 'required|min:8',
                'profileimage' => 'required'
            ];
        }
        
        
        //validation for postlogin
        public function Login(): array {
        return [
            'email' => 'required',
            'password' => 'required',
        ];
    }
    // validation for Forgot Password
     public function  ForgetPassword(): array 
    {
        return [
            'email' => 'required|email|exists:users',
        ];
    }
     // validation for Reset Password
    public function ResetPassword(): array{
        return[
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ];
     }
     public function messages()
     {
         return [
             'name.required' => 'Please Enter your Name',
             'profileimage.required' => "Please select profile image",
             'email.required' => 'Please Enter your Email',
             'password.required' => 'Enter Your Password',
         ];
     }


     protected function failedValidation(Validator $validator)
     {
         throw new HttpResponseException(response()->json([
             'errors' => $validator->errors(),
             'status' => false
           ], 422));
     }
}
