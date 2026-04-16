<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guest_count' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'room_id.required' => 'Please select a room.',
            'room_id.exists' => 'The selected room does not exist.',
            'check_in.required' => 'Please select a check-in date.',
            'check_in.after_or_equal' => 'Check-in date must be today or in the future.',
            'check_out.required' => 'Please select a check-out date.',
            'check_out.after' => 'Check-out date must be after check-in date.',
            'guest_count.required' => 'Please specify the number of guests.',
            'guest_count.min' => 'At least 1 guest is required.',
        ];
    }
}
