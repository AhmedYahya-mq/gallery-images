<?php
namespace Ahmed\GalleryImages\Http\Requests;

use Ahmed\GalleryImages\Contracts\PhotoGetRequestContract;
use Illuminate\Foundation\Http\FormRequest;

class PhotoGetRequest extends FormRequest implements PhotoGetRequestContract
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
     * Get the validation rules for getting photos by IDs.
     *
     * @return array<string, mixed> قواعد التحقق لمصفوفة المعرفات
     */
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:photos,id'],
        ];
    }

    /**
     * Get the photo IDs from the request input.
     *
     * @return array<int> مصفوفة معرفات الصور المطلوبة
     *
     * ملاحظة: يقبل مصفوفة أو عنصر واحد ويعيد دائمًا مصفوفة.
     */
    public function photoIds(): array
    {
        $ids = $this->input('ids', []);
        return is_array($ids) ? $ids : [$ids];
    }
}
