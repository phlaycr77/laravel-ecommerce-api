<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = in_array($this->method(), ['PUT', 'PATCH'], true);

        return [
            'category_id' => [$isUpdate ? 'sometimes' : 'required', 'integer', 'exists:categories,id'],
            'name'        => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'price'       => [$isUpdate ? 'sometimes' : 'required', 'numeric', 'min:0'],
            'stock'       => [$isUpdate ? 'sometimes' : 'required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'price.min'   => 'Giá sản phẩm không được âm.',
            'stock.min'   => 'Số lượng tồn kho không được âm.',
            'image.mimes' => 'Ảnh bìa chỉ chấp nhận định dạng jpg, jpeg, png hoặc webp.',
            'image.max'   => 'Dung lượng ảnh tối đa là 2MB.',
        ];
    }
}