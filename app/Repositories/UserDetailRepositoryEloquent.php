<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\UserDetailRepository;
use App\Validators\UserDetailValidator;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Container\Container as Application;


/**
 * Class UserDetailRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserDetailRepositoryEloquent extends BaseRepository implements UserDetailRepository
{

    public function __construct(
        Application $app
    ) {
        parent::__construct($app);
    }
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    /**
     * Specify Validator class name
     *
     * @return mixed
     */
    public function validator()
    {

        return UserDetailValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getUsers()
    {
        $users = User::orderBy('id', 'desc')->get();

        return $users;
    }
}
