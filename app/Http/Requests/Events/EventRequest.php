<?php

namespace App\Http\Requests\Events;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => 'required|string|max:255',
            'content' => 'required|string',
            'description' => 'required|string|max:1000',
            'featured' => 'boolean',
            'location' => 'required|string|max:255',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'status' => 'required|string|in:active,inactive,cancelled,completed',
            'title' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'category.required' => 'The category field is required.',
            'content.required' => 'The content field is required.',
            'description.required' => 'The description field is required.',
            'location.required' => 'The location field is required.',
            'startDate.required' => 'The start date field is required.',
            'endDate.required' => 'The end date field is required.',
            'endDate.after_or_equal' => 'The end date must be after or equal to the start date.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be one of: active, inactive, cancelled, completed.',
            'title.required' => 'The title field is required.',
        ];
    }
}