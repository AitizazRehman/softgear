<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(\App\Contracts\UserDetailRepository::class, \App\Repositories\UserDetailRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\DepartmentRepository::class, \App\Repositories\DepartmentRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\DesignationRepository::class, \App\Repositories\DesignationRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\OfficeRepository::class, \App\Repositories\OfficeRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\EmploymentDetailRepository::class, \App\Repositories\EmploymentDetailRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\SectionRepository::class, \App\Repositories\SectionRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\QualificationRepository::class, \App\Repositories\QualificationRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\ExperienceRepository::class, \App\Repositories\ExperienceRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\AttendanceRepository::class, \App\Repositories\AttendanceRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\UploadRepository::class, \App\Repositories\UploadRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\HolidayRepository::class, \App\Repositories\HolidayRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\LeaveTypeRepository::class, \App\Repositories\LeaveTypeRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\LeaveRepository::class, \App\Repositories\LeaveRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\OfficeTimingRepository::class, \App\Repositories\OfficeTimingRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\LeaveApplicationRepository::class, \App\Repositories\LeaveApplicationRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\CarryForwardLeaveRepository::class, \App\Repositories\CarryForwardLeaveRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\WeekendRepository::class, \App\Repositories\WeekendRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\ContractDetailRepository::class, \App\Repositories\ContractDetailRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\ProjectRepository::class, \App\Repositories\ProjectRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\AccessPlatformRepository::class, \App\Repositories\AccessPlatformRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\IncomeCategoryRepository::class, \App\Repositories\IncomeCategoryRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\ExpenseCategoryRepository::class, \App\Repositories\ExpenseCategoryRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\IncomeRepository::class, \App\Repositories\IncomeRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\ExpenseRepository::class, \App\Repositories\ExpenseRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\PartyRepository::class, \App\Repositories\PartyRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\OrderRepository::class, \App\Repositories\OrderRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\OrderRepository::class, \App\Repositories\OrderRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\UploadRepository::class, \App\Repositories\UploadRepositoryEloquent::class);
        //:end-bindings:
    }
}
