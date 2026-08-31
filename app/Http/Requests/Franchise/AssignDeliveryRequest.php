<?php

namespace App\Http\Requests\Franchise;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AssignDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'delivery_agent_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $agentId = $this->input('delivery_agent_id');

            if (! $agentId) {
                return;
            }

            $order = $this->route('order');
            $agent = User::find($agentId);

            if (! $agent || ! $agent->hasRole('Delivery Agent')) {
                $validator->errors()->add('delivery_agent_id', 'That account is not a delivery agent.');

                return;
            }

            if ($order && (int) $agent->franchise_id !== (int) $order->franchise_id) {
                $validator->errors()->add('delivery_agent_id', 'That delivery agent belongs to a different franchise.');
            }
        });
    }
}
