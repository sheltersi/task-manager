<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorklogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'task_id'     => ['required', 'integer', 'exists:tasks,id'],
            'work_date'   => ['required', 'date'],
            'hours'       => ['required', 'numeric', 'min:0.25', 'max:24'],
            'started_at'  => ['nullable', 'date_format:H:i'],
            'description' => ['nullable', 'string', 'max:1000'],
            'billable'    => ['nullable', 'boolean'],
        ];
    }
}
