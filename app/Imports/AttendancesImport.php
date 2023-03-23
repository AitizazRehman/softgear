<?php

namespace App\Imports;

use carbon\carbon;
use App\Models\OfficeTiming;
use App\Models\Attendance;
use App\Models\UserDetail;
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
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AttendancesImport implements ToCollection, WithHeadingRow
{
    use Importable;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $request) {
            if ($request['time_in'] != null || $request['time_out'] != null || $request['field_type'] != null || $request['field_in'] != null || $request['field_out'] != null) {
                // dd($request);
                // dd(carbon::parse($request['field_in'])->format('H:i:s'));
                $request['time_in'] != null ? $request['time_in'] = is_numeric($request['time_in'])?(Date::excelToDateTimeObject($request['time_in'])->format('H:i:s')):carbon::parse($request['time_in'])->format('H:i:s') : $request['time_in'] = null;
                $request['time_out'] != null ? $request['time_out'] = is_numeric($request['time_out'])?(Date::excelToDateTimeObject($request['time_out'])->format('H:i:s')):carbon::parse($request['time_out'])->format('H:i:s') : $request['time_out'] = null;
                $request['field_in'] != null ? $request['field_in'] = is_numeric($request['field_in'])?(Date::excelToDateTimeObject($request['field_in'])->format('H:i:s')):carbon::parse($request['field_in'])->format('H:i:s') : $request['field_in'] = null;
                $request['field_out'] != null ? $request['field_out'] = is_numeric($request['field_out'])?(Date::excelToDateTimeObject($request['field_out'])->format('H:i:s')):carbon::parse($request['field_out'])->format('H:i:s') : $request['field_out'] = null;
                $request['official_entry_time'] =  Date::excelToDateTimeObject($request['official_entry_time'])->format('H:i:s');
                $request['official_off_time'] =  Date::excelToDateTimeObject($request['official_off_time'])->format('H:i:s');
                // DB::transaction(function () use ($request, $key) {
                    $user = User::where('st_code', $request['st_code'])->first();
                    if ($user == null) {
                        throw new Exception('Can not Import ' . $request['st_code'] . ' Not Found --> row ' . $key + 1);
                    }
                    $request['user_id'] = $user->id;
                    $half_day_leave = (new CommonFunctionService())->CheckHalfDayLeave($request);
                    $request['date'] != null ? $request['date'] = is_numeric($request['date'])?(Date::excelToDateTimeObject($request['date'])->format('Y-m-d')):carbon::parse($request['date'])->format('Y-m-d') : '';
                    $request['updated_by'] = auth()->user()->id;
                    if ($request['field_type'] != null || $request['field_in'] != null || $request['field_out'] != null) {
                        $request['time_in'] = $request['official_entry_time'];
                        $request['time_out'] = $request['official_off_time'];
                        if ($request['field_in'] == null) {
                            $request['field_in'] = $request['official_entry_time'];
                        }
                        if ($request['field_out'] == null) {
                            $request['field_out'] = $request['official_off_time'];
                        }
                        $request['field_type'] == null ? $request['field_type'] = 'Within region' : '';
                        $request['field_hours'] = ((carbon::parse($request['field_in']))->diff(carbon::parse($request['field_out'])))->format('%H:%I:%S');
                    }
                // dd($request);

                    if (isset($request['time_in']) && isset($request['time_out']) && (carbon::parse($request['time_out'])->gt(carbon::parse($request['time_in'])))) {
                        $request['hours_worked'] = ((carbon::parse($request['time_out']))->diff(carbon::parse($request['time_in'])))->format('%H:%I:%S');
                        if ((carbon::parse($request['time_in']))->gt((carbon::parse($request['official_entry_time']))) && !$half_day_leave) {
                            $request['late'] = (carbon::parse($request['time_in']))->diff((carbon::parse($request['official_entry_time'])))->format('%H:%I:%S');
                        }
                        $office_hours = ((carbon::parse($request['official_entry_time']))->diff(carbon::parse($request['official_off_time'])))->format('%H:%I:%S');
                        $hours_worked = ((carbon::parse($request['time_in']))->diff(carbon::parse($request['time_out'])))->format('%H:%I:%S');
                        if ((carbon::parse($hours_worked))->gt((carbon::parse($office_hours))) && !$half_day_leave) {
                            $request['over_time'] = (carbon::parse($request['time_out']))->diff((carbon::parse($request['official_off_time'])))->format('%H:%I:%S');
                        }
                        // if ((carbon::parse($request['time_out']))->gt((carbon::parse($request['official_off_time'])))) {
                        //     $request['over_time'] = (carbon::parse($request['time_out']))->diff((carbon::parse($request['official_off_time'])))->format('%H:%I:%S');
                        // }

                        if ((carbon::parse($request['time_out']))->lt((carbon::parse($request['official_off_time']))) && !$half_day_leave) {
                            $request['early'] = (carbon::parse($request['time_out']))->diff((carbon::parse($request['official_off_time'])))->format('%H:%I:%S');
                        }
                    }
                    // dd(is_numeric($request['date']));
                    // dd(is_numeric($request['date'])?(Date::excelToDateTimeObject($request['date'])->format('Y-m-d')):carbon::parse($request['date'])->format('Y-m-d'));
                    $request['day'] = Carbon::parse($request['date'])->format('l');
                // dd($request);

                    Attendance::updateOrCreate([
                            'user_id' => $request['user_id'],
                            'date' =>   $request['date'],
                        ], $request->toArray()
                    );
                    // dd("1");
                // });
            }
        }
    }
}
