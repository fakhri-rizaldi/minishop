<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer' => ['required', 'array'],
            'customer.name' => ['required', 'string', 'max:100'],
            'customer.email' => ['required', 'email', 'max:150'],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.product_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('products', 'id')->whereNull('deleted_at'),
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:999'],
        ];
    }

    /**
     * Custom error messages in Indonesian.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'customer.name.required' => 'Nama pembeli wajib diisi.',
            'customer.name.max' => 'Nama pembeli maksimal 100 karakter.',
            'customer.email.required' => 'Email pembeli wajib diisi.',
            'customer.email.email' => 'Format email pembeli tidak valid.',
            'customer.email.max' => 'Email pembeli maksimal 150 karakter.',
            'items.required' => 'Keranjang pesanan tidak boleh kosong.',
            'items.min' => 'Keranjang pesanan minimal berisi 1 item.',
            'items.max' => 'Keranjang pesanan maksimal berisi 50 jenis item.',
            'items.*.product_id.required' => 'ID produk wajib diisi.',
            'items.*.product_id.distinct' => 'Terdapat produk duplikat di dalam pesanan.',
            'items.*.product_id.exists' => 'Produk yang dipilih tidak ditemukan atau sudah tidak tersedia.',
            'items.*.quantity.required' => 'Jumlah produk wajib diisi.',
            'items.*.quantity.integer' => 'Jumlah produk harus berupa bilangan bulat.',
            'items.*.quantity.min' => 'Jumlah produk minimal 1.',
            'items.*.quantity.max' => 'Jumlah produk maksimal 999.',
        ];
    }
}
