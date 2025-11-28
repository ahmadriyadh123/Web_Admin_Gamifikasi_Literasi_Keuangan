<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfilingSubmitRequest extends FormRequest
{
    public function rules()
    {
        return [
            'answers'   => 'required|array|min:3',
            'answers.*' => 'string',
            'profiling_done' => 'boolean|nullable'
        ];
    }

    public function authorize()
    {
        return true;
    }
}
