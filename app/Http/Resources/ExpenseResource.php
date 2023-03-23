<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'updated_by' => $this->updateBy['name'],
            'income_category_id' => $this->income_category_id,
            'expense_category_id' => $this->expense_category_id,
            'party_id' => $this->party_id,
            'payment_method' => $this->payment_method,
            'total_payment' => $this->total_payment,
            'account_head' => $this->expenseCategory['name'],
            'date' => $this->date,
            'date_format' => Carbon::parse($this->date)->format('d-M-Y'),
            'party' => $this->party['party_name'],
            'income_category' => $this->incomeCategory['name'],
          ];
    }
}
