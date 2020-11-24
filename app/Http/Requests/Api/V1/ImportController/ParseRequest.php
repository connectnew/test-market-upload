<?php

namespace App\Http\Requests\Api\V1\ImportController;

use Illuminate\Foundation\Http\FormRequest;

class ParseRequest extends FormRequest
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
            'folder' => 'required|string',
            'start' => 'required|integer',
            'end' => 'required|integer'
        ];
    }
}
