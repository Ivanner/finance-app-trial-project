<?php

namespace App\Http\Requests;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /** @var Transaction $transaction */
        $transaction = $this->route('transaction');
        return  $transaction->user_id == $this->user()->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'label' => 'required',
            'amount' => 'required|numeric',
            'date' => 'required|date'
        ];
    }
}
