<?php

namespace App\Http\Requests;

use App\NullFile;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateImageRequest extends FormRequest
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
        $width = $this['featured_flag'] ? 1920 : 600;
        $height = $this['featured_flag'] ? 1080 : 400;

        return [
            'image' => ['nullable','image', Rule::dimensions()->minWidth($width)->minHeight($height)]
        ];
    }

    /**
     * The validated attributes
     *
     * @return array
     */
    public function atts()
    {
        $path = request('image', new NullFile)->store('images','public');

        return compact('path');
    }
}