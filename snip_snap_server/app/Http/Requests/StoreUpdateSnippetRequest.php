<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateSnippetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true; // Authorization will be handled in the controller
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'required|string',
            'language' => 'required|string|max:50',
            'is_favorite' => 'boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ];

        // If we're updating a snippet, some fields are optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['title'] = 'sometimes|required|string|max:255';
            $rules['code'] = 'sometimes|required|string';
            $rules['language'] = 'sometimes|required|string|max:50';
        }

        return $rules;
    }
}
