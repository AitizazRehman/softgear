<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\OrderCreateRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Contracts\OrderRepository;
use App\Models\Order;
use App\Validators\OrderValidator;
use Exception;

/**
 * Class OrdersController.
 *
 * @package namespace App\Http\Controllers;
 */
class OrdersController extends Controller
{
    /**
     * @var OrderRepository
     */
    protected $repository;

    /**
     * @var OrderValidator
     */
    protected $validator;

    /**
     * OrdersController constructor.
     *
     * @param OrderRepository $repository
     * @param OrderValidator $validator
     */
    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getOrders(Request $request)
    {
        try {
            $users = $this->repository->getOrders($request);
            $response = [
                'details' => $users
            ];
            return response()->json($response);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    public function saveOrder(Request $request)
    {
        try {
            $users = $this->repository->saveOrder($request);
            $response = [
                'details' => $users
            ];
            return response()->json($response);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    public function deleteOrder(Request $request)
    {
        try {
           Order::find($request->id)->delete();
            $response = [
                'message' => 'Order Deleted Successfully',
                'details' =>  null,
            ];
            return response()->json($response);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  OrderCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(OrderCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $order = $this->repository->create($request->all());

            $response = [
                'message' => 'Order created.',
                'data'    => $order->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $order,
            ]);
        }

        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $order = $this->repository->find($id);

        return view('orders.edit', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  OrderUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(OrderUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $order = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'Order updated.',
                'data'    => $order->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {

            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        if (request()->wantsJson()) {

            return response()->json([
                'message' => 'Order deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'Order deleted.');
    }
}
