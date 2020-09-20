<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @bodyParam title string required The title of the Chapter Heading.
 * @bodyParam code_category_id float required The speed of the Chapter Heading.
 * @bodyParam image float required The weight of the Chapter Heading.
 */
class ChapterHeadingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:50',
            'code_category_id' => 'required|numeric|between:0,9999.99',
            'image' => 'required|max:20480|mimes:jpeg,jpg,png,gif',
        ];
    }
}
