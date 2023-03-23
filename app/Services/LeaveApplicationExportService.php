<?php

namespace App\Services;

use App\Models\CarryForwardLeave;
use App\Models\ContractDetail;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use Carbon\Carbon;
use PDF;

class LeaveApplicationExportService
{
    public function leaveApplicationExport($request)
    {
        $leave_details = null;
        $leaves = LeaveApplication::with('leaveType')->where('user_id', $request['user_id'])->where('status', 'approved')->whereYear('date_from', Carbon::parse($request['date_from'])->format('Y'))->get();
        /////////////////////Earned Leave////////////////
        if ($request['leave_type']['name'] == 'Earned Leave' || $request['leave_type']['name'] == 'Leave Encashed') {
            $takenEarnedLeave =  $leaves->filter(function ($item) {
                return $item->leaveType->name == 'Earned Leave';
            })->sum('no_of_days');
            $takenEancashed =  $leaves->filter(function ($item) {
                return $item->leaveType->name == 'Leave Encashed';
            })->count('no_of_days');
            $leave_details['leave_availed'] = $takenEarnedLeave;
            $leave_details['leave_encashed'] = $takenEancashed;
            $leave_details['credit_balance'] = $this->creditBalanceTillDate($request);
            $leave_details['balance'] = $this->openingBalanceEarned($request);
            $leave_details['available_leaves'] = ($this->creditBalanceTillDate($request) + $this->openingBalanceEarned($request)) - ($leave_details['leave_availed'] + $leave_details['leave_encashed']);
        }
        /////////////////////Sick Leave////////////////
        else if ($request['leave_type']['name'] == 'Sick Leave') {
            $takenSickLeave =  $leaves->filter(function ($item) {
                return $item->leaveType->name == 'Sick Leave';
            })->sum('no_of_days');
            $leave_details['leave_availed'] = $takenSickLeave;
            $leave_details['leave_encashed'] = 0;
            $leave_details['credit_balance'] = $this->creditBalanceTillDate($request);
            $leave_details['balance'] = $this->openingBalanceSick($request);
            $leave_details['available_leaves'] = ($this->creditBalanceTillDate($request) + $this->openingBalanceSick($request)) - ($leave_details['leave_availed']);
        }
        /////////////////////Casual Leave////////////////
        else if ($request['leave_type']['name'] == 'Casual Leave') {
            $takenCasualLeave =  $leaves->filter(function ($item) {
                return $item->leaveType->name == 'Casual Leave';
            })->sum('no_of_days');
            $leave_details['leave_availed'] = $takenCasualLeave;
            $leave_details['leave_encashed'] = '';
            $leave_details['credit_balance'] = $this->creditBalanceTillDate($request);
            $leave_details['balance'] = '';
            $leave_details['available_leaves'] = $this->creditBalanceTillDate($request) - ($leave_details['leave_availed']);
        }
        /////////////////////Maternity Leave////////////////
        else if ($request['leave_type']['name'] == 'Maternity Leave') {
            $takenMaternityLeave =  $leaves->filter(function ($item) {
                return $item->leaveType->name == 'Maternity Leave';
            })->sum('no_of_days');
            $leave_details['leave_availed'] = $takenMaternityLeave;
            $leave_details['leave_encashed'] = '';
            $leave_details['credit_balance'] = $this->creditBalanceTillDate($request);
            $leave_details['balance'] = '';
            $leave_details['available_leaves'] = $this->creditBalanceTillDate($request) - ($leave_details['leave_availed']);
        }
        /////////////////////Paternity Leave////////////////
        else if ($request['leave_type']['name'] == 'Paternity Leave') {
            $takenPaternityLeave =  $leaves->filter(function ($item) {
                return $item->leaveType->name == 'Paternity Leave';
            })->sum('no_of_days');
            $leave_details['leave_availed'] = $takenPaternityLeave;
            $leave_details['leave_encashed'] = '';
            $leave_details['credit_balance'] = $this->creditBalanceTillDate($request);
            $leave_details['balance'] = '';
            $leave_details['available_leaves'] = $leave_details['credit_balance'] - ($leave_details['leave_availed']);
        }
        /////////////////////Leave Without Pay////////////////
        else if ($request['leave_type']['name'] == 'Leave Without Pay') {
            $takenMaternityLeave =  $leaves->filter(function ($item) {
                return $item->leaveType->name == 'Leave Without Pay';
            })->sum('no_of_days');
            $leave_details['leave_availed'] = $takenMaternityLeave;
            $leave_details['leave_encashed'] = '';
            $leave_details['credit_balance'] = '';
            $leave_details['balance'] = '';
            $leave_details['available_leaves'] = '';
        }
        else if ($request['leave_type']['name'] == 'Examination Leave') {
            $takenExaminationLeave =  $leaves->filter(function ($item) {
                return $item->leaveType->name == 'Examination Leave';
            })->sum('no_of_days');
            $leave_details['leave_availed'] = $takenExaminationLeave;
            $leave_details['leave_encashed'] = '';
            $leave_details['credit_balance'] = '';
            $leave_details['balance'] = '';
            $leave_details['available_leaves'] = '';
        }
        else if ($request['leave_type']['name'] == 'Compensatory Leave') {
            $takenCompensatorLeave =  $leaves->filter(function ($item) {
                return $item->leaveType->name == 'Compensatory Leave';
            })->sum('no_of_days');
            $leave_details['leave_availed'] = $takenCompensatorLeave;
            $leave_details['leave_encashed'] = '';
            $leave_details['credit_balance'] = '';
            $leave_details['balance'] = '';
            $leave_details['available_leaves'] = '';
        }
        $request['leaveDetails'] =  $leave_details;
        $header = [
            'Content-Type' => 'application/*',
        ];
        view()->share('application_detail', $request);
        $pdf = PDF::loadView('exports.export-leave-application', $request);
        $path = 'leave-application.pdf';
        $pdf->save($path);
        $file_data = [];
        $file_data['path'] = $path;
        $file_data['header'] =  $header;
        return $file_data;
    }
    public function creditBalanceTillDate($request)
    {   
        $contract = ContractDetail::where('user_id',$request['user_id'])->where('active',1)->first();
        $request['contract_id'] = $contract->id;
        $duration =  (new CommonFunctionService())->employeContractDuration($request);
        $leaveType = LeaveType::where('name', $request['leave_type']['name'])->first();
        // $credit_balance = Round($leaveType->no_of_days / 365 * $duration, 0);
        $credit_balance = Round((($leaveType->no_of_days*2)*$duration/365), 0)/2;
        return  $credit_balance;
    }
    public function openingBalanceEarned($request)
    {
        $leave =  CarryForwardLeave::where('user_id', $request['user_id'])->where('total_earned', '!=', null)->where('year', carbon::parse($request['date_from'])->subYear()->format('Y'))->first();
        $balance = $leave ? $leave->total_earned : 0;
        return $balance;
    }
    public function openingBalanceSick($request)
    {
        $leave =  CarryForwardLeave::where('user_id', $request['user_id'])->where('balance_available', '!=', null)->where('year', carbon::parse($request['date_from'])->subYear()->format('Y'))->first();
        $balance = $leave ? $leave->balance_available : 0;
        return $balance;
    }
}
