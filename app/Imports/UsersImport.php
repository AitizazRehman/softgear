<?php

namespace App\Imports;

use App\Models\ContractDetail;
use App\Models\Department;
use App\Models\Designation;
use App\Models\EmploymentDetail;
use App\Models\Office;
use App\Models\Project;
use App\Models\UserDetail;
use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Spatie\Permission\Models\Role;

class UsersImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnError
{
    use Importable, SkipsErrors;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $key => $value) {
            if(isset($value['employee_id']))
            {

                // $request = [];
                // echo $key+1 .'<br>';
                $request['st_code'] = $value['employee_id'];
                $request['first_name'] = $value['first_name'];
                $request['last_name'] = $value['last_name'];
                $request['middle_name'] = $value['middle_name'];
                $request['salutation'] = $value['gender'] == 'Male' ? 'Mr.' : 'Ms.';
                $request['email'] = $value['official_e_mail_ids'];
                $request['password'] = trim($request['first_name']).'@123';
                $request['confirm'] = trim($request['first_name']).'@123';
                $request['active'] = 1;
                $request['gender'] = $value['gender'];
                $request['cnic'] = $value['cnic_no'];
                $request['nationality'] = 'Pakistan';
                $request['father_name'] = $value['fathers_name_husband_name'];
                $request['joining_date'] = Date::excelToDateTimeObject($value['joining_date_mmddyyyy'])->format('Y-m-d');
                $request['employment_status'] = $value['employment_type'];

                $found = User::where('st_code', $request['st_code'])->first();
                // dd($found);
                if($found != null) {
                    echo 'updating the information Already exist user<br>';
                }
                $user = User::updateOrCreate([
                    'st_code'=> $request['st_code']
                    ], $request);

                if(isset($value['project_name']) && isset($value['contract_start_date_mmddyyyy']) && isset($value['contract_end_date_mmddyyyy'])){
                    $project = Project::updateOrCreate([
                        'name' => trim($value['project_name'])
                    ],[
                        'name' => trim($value['project_name'])
                    ]);
                    ContractDetail::create([
                        'user_id'=> $user->id,
                        'project_id' => $project->id,
                        'contract_start_date' => Date::excelToDateTimeObject($value['contract_start_date_mmddyyyy'])->format('Y-m-d'),
                        'contract_end_date' => Date::excelToDateTimeObject($value['contract_end_date_mmddyyyy'])->format('Y-m-d'),
                        'active' => 1
                    ]);

                }else{
                    echo $key .'Project/contract details are incorrect<br>';
                }
                $roles = Role::where('name', 'user')->first();
                $user->syncRoles($roles);
                $request['user_id'] = $user->id;
                UserDetail::updateOrCreate([
                    'user_id'=>$user->id
                ], $request );

                $office = Office::where('name', trim($value['office_location']))->first();
                $request['office_id'] = $office->id;
                $department = Department::where('name', trim($value['department_name']))->first();
                $designation = Designation::where('name', trim($value['position']))->first();
                if($department == null) {
                    echo 'Not Found----' .$value['department_name'];
                    $department = Department::create([
                        'name' => trim($value['department_name'])
                    ]);
                }
                if($designation == null) {
                    echo 'Not Found----' .$value['position'];
                    $designation = Designation::create([
                        'name' => trim($value['position'])
                    ]);
                }
                $request['department_id'] = $department->id;
                $request['designation_id'] = $designation->id;
                if(isset($value['administrative_reporting'])) {
                    $administrative = User::where('st_code', $value['administrative_reporting'])->first();
                    $request['administrative_reporting'] = $administrative->id;
                }elseif(isset($value['functional_reporting'])) {
                    $functional = User::where('st_code', $value['functional_reporting'])->first();
                    $request['functional_reporting'] = $functional->id;
                }
                EmploymentDetail::updateOrCreate([
                    'user_id'=>$request['user_id']
                ],
                 $request
                );
            }

            }
        });
        return;
    }
    public function rules(): array
    {
        return [
            // '*.date' => ['date_format:Y-m-d', 'required'],
            // '*.first_name' => ['first_name', 'required'],
            // '*.employee_id' => ['required', 'exists:user_details,st_code'],
            // '*.time_in' => ['date_format:H:i:s', 'nullable', 'required_with:*.time_out'],
            // '*.time_out' => ['date_format:H:i:s', 'after:*.time_in', 'nullable', 'required_with:*.time_in'],
            // '*.field_in' => ['date_format:H:i:s', 'nullable', 'required_with:*.filed_out'],
            // '*.field_out' => ['date_format:H:i:s', 'after:*.field_in', 'nullable', 'required_with:*.field_in'],
        ];
    }
}
