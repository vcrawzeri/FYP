<?php

namespace App\Http\Controllers\Api;

use App\Enum\CustomerStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerListResource;
use App\Http\Resources\CustomerResource;
use App\Models\CustomerAddress;
use App\Enum\TypeAddress;
use App\Models\Country;
use App\Models\Customer;

use Illuminate\Support\Facades\DB;


class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $search = request('search', false);
            $perPage = request('per_page', 10);
            $sortField = request('sort_field', 'updated_at');
            $sortDirection = request('sort_direction', 'desc');

            // Join with users table to get email only
            $query = Customer::query()
                ->join('users', 'customers.user_id', '=', 'users.id')
                ->select('customers.*', 'users.email'); // select customer fields + email only

            // Apply sorting
            $query->orderBy($sortField, $sortDirection);

            // Apply search
            if ($search) {
                $query->where(DB::raw("CONCAT(COALESCE(customers.first_name,''), ' ', COALESCE(customers.last_name,''))"), 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%")
                    ->orWhere('customers.phone', 'like', "%{$search}%"); // keep customer phone
            }

            return CustomerListResource::collection($query->paginate($perPage));
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }




    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerRequest $request, Customer $customer)
    {
        $customerData = $request->validated();
        $customerData['updated_by'] = $request->user()->id;
        $customerData['status'] = $customerData['status'] ? CustomerStatus::Active->value : CustomerStatus::Disabled->value;
        $shippingData = $customerData['shippingAddress'];
        $billingData = $customerData['billingAddress'];

        // Update customer info
        $customer->update($customerData);

        // Update or create shipping address
        if ($customer->shippingAddress) {
            $customer->shippingAddress->update($shippingData);
        } else {
            $shippingData['customer_id'] = $customer->user_id;
            $shippingData['type'] = TypeAddress::Shipping->value;
            CustomerAddress::create($shippingData);
        }

        // Update or create billing address
        if ($customer->billingAddress) {
            $customer->billingAddress->update($billingData);
        } else {
            $billingData['customer_id'] = $customer->user_id;
            $billingData['type'] = TypeAddress::Billing->value;
            CustomerAddress::create($billingData);
        }

        return new CustomerResource($customer);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->noContent();
    }

    public function countries()
    {
        return  Country::query()->orderBy('name','asc')->get();



    }

}
