<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContentGenerationRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'facebook_page_id' => 'required|exists:facebook_pages,id',
            'content_type' => 'required|in:post,story,reel',
            'topic' => 'required|string|max:500',
            'tone' => 'required|in:professional,casual,friendly,authoritative',
            'include_image' => 'boolean',
            'industry' => 'nullable|string|max:100',
            'target_audience' => 'nullable|string|max:500',
            'business_goals' => 'nullable|string|max:500',
            'forbidden_words' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'facebook_page_id.required' => 'Please select a Facebook page.',
            'facebook_page_id.exists' => 'Selected page does not exist.',
            'content_type.required' => 'Please select a content type.',
            'topic.required' => 'Please provide a topic for the content.',
            'topic.max' => 'Topic must not exceed 500 characters.',
            'tone.required' => 'Please select a tone for the content.',
        ];
    }
}
