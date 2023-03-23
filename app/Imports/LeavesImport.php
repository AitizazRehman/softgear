<?php

namespace App\Imports;

use App\Models\LeaveType;
use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use App\Contracts\LeaveApplicationRepository;
use Exception;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;



class LeavesImport implements ToCollection, WithHeadingRow
{
    use Importable;
    protected $leaveApplicationRepository;

    public function __construct(LeaveApplicationRepository $leaveApplicationRepository)
    {
        $this->leaveApplicationRepository = $leaveApplicationRepository;
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        // dd($rows[11]);
        foreach ($rows as $key => $value) {
            // dd(Date::excelToDateTimeObject($rows[25]['date_to'])->format('Y-m-d'));
            if(isset($value['stcode']))
            {
            DB::transaction(function () use ($key, $value) {
                $user = User::where('st_code', $value['stcode'])->first();
                if($user != null) {
                    if($value['date_from'] != null && $value['date_to'] != null) {
                        $migrateData = [];
                        // dd($value['date_to']);
                        // dd(Date::excelToDateTimeObject($value['date_to'])->format('Y-m-d'));
                        $migrateData['date_from'] = is_numeric($value['date_from']) ? Date::excelToDateTimeObject($value['date_from'])->format('Y-m-d'): Carbon::parse($value['date_from'])->format('Y-m-d');
                        $migrateData['date_to'] = is_numeric($value['date_from']) ? Date::excelToDateTimeObject($value['date_to'])->format('Y-m-d'): Carbon::parse($value['date_to'])->format('Y-m-d');
                        if($value['half_day'] == 'Yes') {
                            $migrateData['half_day_leave'] =  1;
                        }
                        $migrateData['user_id'] = $user['id'];
                        $leavetype = LeaveType::where('name', ucwords($value['leave_type']))->first();
                        if ($leavetype != null) {
                            $migrateData['status'] = 'approved';
                            $migrateData['leave_type_id'] = $leavetype->id;
                            //  dd($migrateData);
                            // $leaveApplicationRepository = new LeaveApplicationRepository;
                            $this->leaveApplicationRepository->storeLeave($migrateData);
                        } else{
                           throw  new Exception("Leaves not found:" . $value['leave_type']);
                        }
                    }else{
                        throw  new Exception('Date From or Date to Missing on line no'.' '.$key++);
                    }
                }else{
                    throw  new Exception('st code not found'.' '.$value['stcode']);
                }
            });
        }else{
            throw  new Exception('st code format wrong!');
        }
    }

        // exit ;
    }
}