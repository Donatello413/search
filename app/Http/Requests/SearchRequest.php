<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use function Laravel\Prompts\search;

class SearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string',
        ];
    }
}
