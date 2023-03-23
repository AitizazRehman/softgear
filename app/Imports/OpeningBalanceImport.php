<?php

namespace App\Imports;

use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Contracts\CarryForwardLeaveRepository;
use PhpOffice\PhpSpreadsheet\Shared\Date;



class OpeningBalanceImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnError
{
    use Importable, SkipsErrors;
    protected $carryForwardLeaveRepository;

    public function __construct(CarryForwardLeaveRepository $carryForwardLeaveRepository)
    {
        $this->carryForwardLeaveRepository = $carryForwardLeaveRepository;
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $value) {
            DB::transaction(function () use ($key, $value) {
                $user = User::where('st_code', $value['st_code'])->first();
                if($user != null) {
                    $value['user_id'] = $user->id;
                    $value['credit_or_cancellation_sick_l'] = $value['credit_or_cancellation_sick_leave'];
                    $value['balance_available'] = $value['balance_available_sick_leave'];
                    $value['un_earned_encashment'] = $value['unearned_encashment'];
                    $value['total_earned'] = $value['total_earned_leave'];
                    $this->carryForwardLeaveRepository->storeCarryForwardLeave($value->toArray(),null);
                    echo 'found' . '<br>';
                }else{
                    echo 'not found' . '<br>';
                }
            });
        }
    }
    public function rules(): array
    {
        return [
            // '*.Stcode' => ['required', 'exists:user_details,Stcode'],
        ];
    }
}