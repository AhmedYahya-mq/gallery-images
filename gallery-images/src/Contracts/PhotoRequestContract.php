<?php

namespace Ahmed\GalleryImages\Contracts;

/**
 * Interface PhotoRequestContract
 * This interface defines the contract for photo upload requests.
 * It includes methods for validation rules, authorization, custom attributes, and messages.
 * @package Ahmed\MyImagePackage\Contracts\Request
 */
interface PhotoRequestContract
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     * قواعد التحقق للملفات المرفوعة (صور متعددة).
     */
    public function rules(): array;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool true إذا كان المستخدم مخولًا
     */
    public function authorize(): bool;

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string> أسماء الحقول المخصصة للأخطاء
     */
    public function attributes(): array;

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string> رسائل مخصصة للأخطاء
     */
    public function messages(): array;
}
