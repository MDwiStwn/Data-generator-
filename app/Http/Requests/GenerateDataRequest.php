<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\GenerateMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'category' => [
                'required',
                Rule::in(['pegawai', 'murid', 'tagihan']),
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:500',
            ],
            'mode' => [
                'nullable',
                Rule::in(array_map(fn($m) => $m->value, GenerateMode::availableModes())),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'category.required' => 'Pilih template terlebih dahulu.',
            'category.in' => 'Template yang dipilih tidak valid.',
            'quantity.required' => 'Jumlah data harus diisi.',
            'quantity.integer' => 'Jumlah data harus berupa angka.',
            'quantity.min' => 'Jumlah data minimal 1.',
            'quantity.max' => 'Jumlah data maksimal 500.',
            'mode.in' => 'Mode generate tidak valid.',
        ];
    }
}
