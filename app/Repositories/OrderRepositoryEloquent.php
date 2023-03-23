<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\OrderRepository;
use App\Contracts\UploadRepository;
use App\Models\Order;
use App\Validators\OrderValidator;

/**
 * Class OrderRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderRepositoryEloquent extends BaseRepository implements OrderRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    private $uploadRepository;

    public function __construct(
        UploadRepository $uploadRepository
    ) {
        $this->uploadRepository = $uploadRepository;
    }
    public function model()
    {
        return Order::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    public function getOrders($request)
    {
        return Order::with('upload')->when(isset($request->order_id),function($q1) use($request){
            $q1->where('id',$request->order_id);
        })
        ->get();
    }
    public function saveOrder($request)
    {
        $data = json_decode($request['data'], true);
        $order = Order::updateOrCreate(
            [
                'id' => isset($data['id'])?$data['id']:null
            ],
            $data
        );
        if (isset($request['image'])) {
            $upload = $request['image'];
            $this->uploadRepository->upload($upload, $order);
        }
    }
}
