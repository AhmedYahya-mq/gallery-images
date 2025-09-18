<?php
namespace Ahmed\GalleryImages\Http\Requests;

use Ahmed\GalleryImages\Contracts\PhotoRequestContract;
use Ahmed\GalleryImages\Rules\UniquePhotoName;
use Illuminate\Foundation\Http\FormRequest;

class PhotoRequest extends FormRequest implements PhotoRequestContract
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool true إذا كان المستخدم مخولًا
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     * قواعد التحقق للملفات المرفوعة (صور متعددة).
     */
    public function rules(): array
    {
        return [
            'files' => ['required', 'array', 'min:1'],
            // bail: stop on first failure; file: ensure uploaded file;
            // mimetypes + mimes: check both content-type and extension; max is in KB (5 MB)
            'files.*' => [
                'bail', 'required', 'file',
                'mimetypes:image/jpeg,image/png,image/gif,image/webp,image/svg+xml,image/avif', 'mimes:jpeg,jpg,png,gif,webp,svg,avif', 'max:5120',
                 new UniquePhotoName()],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string> رسائل مخصصة للأخطاء
     */
    public function messages(): array
    {
        return [
            'files.required' => 'يجب اختيار صور للرفع',
            'files.array' => 'الملفات يجب أن تكون في شكل مصفوفة',
            'files.min' => 'يجب اختيار صورة واحدة على الأقل',
            'files.*.required' => 'يجب اختيار صورة للرفع',
            'files.*.file' => 'الملف المرفوع غير صالح',
            'files.*.mimetypes' => 'نوع الصورة غير مدعوم (jpeg, png, gif, webp, svg, avif)',
            'files.*.mimes' => 'امتداد الملف غير مدعوم (jpeg, jpg, png, gif, webp, svg, avif)',
            'files.*.max' => 'حجم الصورة يجب ألا يتجاوز 5 ميجابايت',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string> أسماء الحقول المخصصة للأخطاء
     */
    public function attributes(): array
    {
        return [
            'files' => 'الصور',
            'files.*' => 'الصورة',
        ];
    }
}
