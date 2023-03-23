<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class UserCreateRequest extends FormRequest
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
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8|max:255',

          ];
    }
    public function validationData()
    {
         $this->merge(json_decode($this->input('data'),true));
         return $this->all();
    }

    public function attributes()
    {
       
    }
    
}
