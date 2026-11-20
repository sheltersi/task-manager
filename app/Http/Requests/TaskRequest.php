<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status'      => ['required', 'in:to_do,in_progress,in_review,completed'],
            'priority'    => ['required', 'integer', 'min:1', 'max:5'],
            'due_date'    => ['nullable', 'date'],
        ];
    }
}
