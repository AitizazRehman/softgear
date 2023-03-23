<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;


class DynamicsService
{
    private $access_token;

    public function __construct()
    {
        $this->base_uri = config('services.dynamics.resource');
    }
    public function getAccessToken()
    {
        $response = Http::asForm()->post('https://login.windows.net/'.config('services.dynamics.tenenat_id').'/oauth2/token',
            [
                'client_id' => config('services.dynamics.client_id'),
                'client_secret' => config('services.dynamics.client_secret'),
                'grant_type' => config('services.dynamics.grant_type'),
                'resource' => config('services.dynamics.resource')
            ]);
        $result = json_decode($response->getBody()->getContents());
        // set Access Token
        // return $result->access_token;
        $this->setAccessToken($result->access_token);
        // Get User Data
        return $result;
    }

    public function setAccessToken($token)
    {
        $this->access_token = $token;
    }

    public function getStaffProfiles($request)
    {
        if ($this->access_token) {
            $response = Http::withToken($this->access_token)->post($this->base_uri .'api/services/HcmEmployeeSvcGroup/HcmEmployeeSvcGroup/getEmployeeList', [
                    "_asOfDate" => $request['as_of_date']
            ]);
            return json_decode($response->getBody()->getContents(), true);
        }
        return [];
    }
    
    
}