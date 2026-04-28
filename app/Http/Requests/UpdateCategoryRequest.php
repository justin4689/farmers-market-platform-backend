<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = (int) $this->route('id');

        return [
            'name'      => ['required', 'string', 'max:255'],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->whereNot('id', $id),
                function ($attribute, $value, $fail) use ($id) {
                    if ((int) $value === $id) {
                        $fail('A category cannot be its own parent.');
                    }
                },
            ],
        ];
    }
}
