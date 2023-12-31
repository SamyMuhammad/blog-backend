<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $bodyWithoutTags = strip_tags($this->body);
        return $this->merge(['body' => strlen($bodyWithoutTags) ? $this->body : '']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "title" => ['required', 'string', 'min:3', 'max:255'],
            "body" => ['required', 'string'],
            "cover" => ['required', 'image', 'max:6144'],
        ];
    }
}
