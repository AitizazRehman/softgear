<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\AccessPlatformRepository;
use App\Models\AccessPlatform;
use App\Validators\AccessPlatformValidator;

/**
 * Class AccessPlatformRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AccessPlatformRepositoryEloquent extends BaseRepository implements AccessPlatformRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AccessPlatform::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function store($data, $provider)
    {
        return $this->updateOrCreate([
                'provider' => $provider
            ],[
                'provider' => $provider,
                'access_token' => $data->access_token,
                'token_type' => $data->token_type,
                'meta' => json_encode($data)
            ]);
    }
    
}
