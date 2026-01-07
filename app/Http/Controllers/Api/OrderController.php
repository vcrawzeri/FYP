<?php

namespace App\Http\Controllers\Api;

use App\Enum\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Resources\OrderListResource;
use App\Http\Resources\OrderResource;
use App\Mail\OrderUpdateEmail;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index()
    {
        $search = request('search', '');
        $perPage = (int) request('per_page', 10);
        $sortField = request('sort_field', 'updated_at');
        $sortDirection = request('sort_direction', 'desc');

        $allowedSorts = ['id', 'total_price', 'status', 'created_at', 'updated_at'];
        $sortField = in_array($sortField, $allowedSorts) ? $sortField : 'updated_at';
        $sortDirection = $sortDirection === 'asc' ? 'asc' : 'desc';

        $query = Order::with(['user.customer', 'items'])
            ->orderBy($sortField, $sortDirection);

        if ($search) {
            $query->where(function ($q) use ($search) {

                // Search by order ID
                if (is_numeric($search)) {
                    $q->where('id', $search);
                }

                // ✅ CORRECT: search through user → customer
                $q->orWhereHas('user.customer', function ($customer) use ($search) {
                    $customer->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });

                // Optional: search by user email
                $q->orWhereHas('user', function ($user) use ($search) {
                    $user->where('email', 'like', "%{$search}%");
                });
            });
        }

        return OrderListResource::collection(
            $query->paginate($perPage)
        );
    }


    public function view(Order $order)
    {
        return new OrderResource($order);
    }

    public function getStatuses()
    {
        return OrderStatus::getStatuses();
    }

    public function changeStatus(Order $order, $status)
    {
        $order->status = $status;
        $order->save();

        if ($order->user) {
            Mail::to($order->user)->send(new OrderUpdateEmail($order));
        }

        return response('', 200);
    }
}
