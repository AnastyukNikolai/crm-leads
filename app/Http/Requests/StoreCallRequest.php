<?php

namespace App\Http\Requests;

use App\Enums\CallResult;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCallRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'duration' => ['required', 'integer', 'min:0'],
            'result' => ['required', Rule::enum(CallResult::class)],
            'manager_id' => ['required', 'integer', 'exists:managers,id'],
        ];
    }
}
