<?php

namespace App\Services;

use App\Models\ContractDetail;
use App\Models\LeaveApplication;
use Carbon\Carbon;

class CommonFunctionService
{
    public function employeContractDuration($request)
    {
        $duration = null;
        $employmentDetail = ContractDetail::where('user_id', $request['user_id'])->where('id', $request['contract_id'])->first();
        if ((Carbon::parse($employmentDetail->contract_end_date))->lt(carbon::now()->format('Y-m-d'))) {
            $duration = (carbon::parse($employmentDetail->contract_start_date))->diffInDays(carbon::parse($employmentDetail->contract_end_date)->addDay());
        } else if ((carbon::parse($employmentDetail->contract_end_date))->gt(carbon::now()->format('Y-m-d'))) {
            $duration = (carbon::parse($employmentDetail->contract_start_date))->diffInDays(carbon::now()->addDay()->format('Y-m-d'));
        }
        return $duration;
    }
    public function CheckHalfDayLeave($request)
    {
        $leave_application = LeaveApplication::where('user_id', $request['user_id'])->where('half_day_leave', 'Yes')
            ->whereHas('leaves',function($q4) use($request){
                $q4->where('date',$request['date']);
            })->first();
        return !$leave_application?false:true;
    }
}
