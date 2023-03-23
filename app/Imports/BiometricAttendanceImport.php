<?php

namespace App\Imports;

use carbon\carbon;
use App\Models\Attendance;
use App\Models\OfficeTiming;
use App\Services\CommonFunctionService;
use App\User;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
// use Maatwebsite\Excel\Concerns\SkipsErrors;
// use Maatwebsite\Excel\Concerns\SkipsOnError;
// use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class BiometricAttendanceImport implements ToCollection, WithHeadingRow
{
    use Importable;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    private $timing; 
    private $office_timing; 

    public function __construct($data)
    {
        $this->office_timing = $data; 
    }
    public function collection(Collection $rows)
    {
            foreach ($rows as $key => $request) {
                DB::transaction(function () use ($key, $request) {
                    $data = null;
                    $user = User::where('st_code', $request['st_code'])->first();
                    if($user == null){
                        throw new Exception('Can not Import '. $request['st_code'] . ' Not Found --> row '. $key+1);
                    }
                    if(count($this->office_timing) > 0)
                    {   
                        $data['office_entry_time'] = $this->office_timing['offical_entry_time'];
                        $data['office_off_time'] = $this->office_timing['offical_off_time'];
                    }
                    else if ($user->employmentDetail && $user->employmentDetail->office && $user->employmentDetail->office->OfficeTiming) {
                        $this->timing = $user->employmentDetail->office->OfficeTiming->where('active','1')->first();
                        $data['office_entry_time'] = $this->timing->offical_entry_time;
                        $data['office_off_time'] = $this->timing->offical_off_time;
                    } 
                    else{
                        throw  new Exception('Staff Office Location is missing OR Office Timing is not active for Staff');
                    }
                    $data['user_id'] = $user->id;
                    $timestamp = explode(' ', $request['time']);
                    $timestamp[1] = $timestamp[1] . ' ' . $timestamp[2];
                    // dd($timestamp);
                    $data['time'] = carbon::parse($timestamp[1])->format('H:i:s');
                    $data['date'] = carbon::parse($timestamp[0])->format('Y-m-d');
                    $data['day'] = carbon::parse($timestamp[0])->format('l');
                    // echo $key . '<br>';
                    $data['updated_by'] = auth()->user()->id;
                    if ($request['state'] == 'C/In') {
                        $data['time_in'] = $data['time'];
                    } elseif ($request['state'] == 'Out') {
                        $data['field_in'] = $data['time'];
                        $data['time_in'] = $data['office_entry_time'];
                    } else if ($request['state'] == 'C/Out') {
                        $data['time_out'] = $data['time'];
                    } else if ($request['state'] == 'Out Back') {
                        $data['field_out'] = $data['time'];
                        $data['time_out'] = $data['office_off_time'];
                    }
                    $this->storeAttendance($data);

                    $this->attendanceCalculation($data);
                });
            }
    }
    public function attendanceCalculation($row)
    {
        // $attendances = Attendance::get()->chunk(300);
        // dd($row);
        DB::transaction(function () use ($row) {
            // foreach ($rows as $row) {
                $data = $row;
                // $user = User::where('st_code', $row['stcode'])->first();
                // if($user != null){
                    // $timestamp = explode(' ', $row['time']);
                    // $timestamp[1] = $timestamp[1] . ' ' . $timestamp[2];
                    // $data['date'] = carbon::parse($timestamp[0])->format('Y-m-d');
                    // $data['user_id'] = $user->id;
                    $attendance = Attendance::where('date', $row['date'])
                                            ->where('user_id', $row['user_id'])->first();
                    $half_day_leave = (new CommonFunctionService())->CheckHalfDayLeave($row);
                    if ($attendance['field_in'] && $attendance['field_out']) {
                        $data['field_hours'] = ((carbon::parse($attendance['field_in']))->diff(carbon::parse($attendance['field_out'])))->format('%H:%I:%S');
                    }
                    if ((carbon::parse($attendance['time_in']))->gt((carbon::parse($row['office_entry_time']))) && !$half_day_leave) {
                        $data['late'] = (carbon::parse($attendance['time_in']))->diff((carbon::parse($row['office_entry_time'])))->format('%H:%I:%S');
                    }
                    if ($attendance['time_in'] && $attendance['time_out']) {
                            $data['hours_worked'] = ((carbon::parse($attendance['time_out']))->diff(carbon::parse($attendance['time_in'])))->format('%H:%I:%S');
                        $office_hours = ((carbon::parse($row['office_entry_time']))->diff(carbon::parse($row['office_off_time'])))->format('%H:%I:%S');
                        $hours_worked = ((carbon::parse($attendance['time_in']))->diff(carbon::parse($attendance['time_out'])))->format('%H:%I:%S');
                        if ((carbon::parse($hours_worked))->gt((carbon::parse($office_hours))) && !$half_day_leave) {
                            $data['over_time'] = (carbon::parse($attendance['time_out']))->diff((carbon::parse($row['office_off_time'])))->format('%H:%I:%S');
                        }
                        // if ((carbon::parse($attendance['time_out']))->gt((carbon::parse($row['office_entry_time'])))) {
                        //     $data['over_time'] = (carbon::parse($attendance['time_out']))->diff((carbon::parse($row['office_off_time'])))->format('%H:%I:%S');
                        // }

                        if ((carbon::parse($attendance['time_out']))->lt((carbon::parse($row['office_off_time']))) && !$half_day_leave) {
                            $data['early'] = (carbon::parse($attendance['time_out']))->diff((carbon::parse($row['office_off_time'])))->format('%H:%I:%S');
                        }
                    }

                    $this->storeAttendance($data);
                    // dd($row);

                // } else{
                //     echo $row['stcode'] . "not found inactive" . '<br>';
                // }
            // }
        });
        return ;
    }
    public function storeAttendance($request)
    {
        // dd($request);
        return Attendance::updateOrCreate(
            [
                'user_id'   => $request['user_id'],
                'date' =>  $request['date'],
            ],
            $request
        );
    }
   
}
