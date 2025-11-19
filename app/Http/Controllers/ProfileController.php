<?php

namespace App\Http\Controllers;

use App\Enum\TypeAddress;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\PasswordUpdateRequest;
use App\Models\Country;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function view(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        /** @var \App\Models\Customer $customer */
        $customer = $user->customer;
        $shippingAddress = $customer->shippingAddress ?: new CustomerAddress(['type' => TypeAddress::Shipping]);
        $billingAddress = $customer->billingAddress ?: new CustomerAddress(['type' => TypeAddress::Billing]);
        //dd($customer,$shippingAddress->attributesToArray(), $billingAddress,$billingAddress->customer);
        $countries = Country::query()->orderBy('name')->get();
        return view('profile.view',compact('customer','user','shippingAddress','billingAddress','countries'));
    }

    public function store(ProfileRequest $request)
    {
        //nested data
        $customerData = $request->validated();
        $shippingData = $customerData['shipping'];
        $billingData = $customerData['billing'];

        /** @var \App\Models\User $user */
        $user = $request->user();
        /** @var \App\Models\Customer $customer */
        $customer = $user->customer;
        //i also need to check if the customer address doesnot exists then i create new customer address with the type shipping

        // then i call update on the customer
        $customer->update($customerData);
        if($customer->shippingAddress){
            $customer->shippingAddress->update($shippingData);
        }else{
            $shippingData['customer_id'] = $customer->user_id;
            $shippingData['type'] = TypeAddress::Shipping->value;
            CustomerAddress::create($shippingData);
        }

        // Billing Address
        if ($customer->billingAddress) {
            $customer->billingAddress->update($billingData);
        } else {
            $billingData['customer_id'] = $customer->user_id; // correct foreign key
            $billingData['type'] = TypeAddress::Billing->value;
            CustomerAddress::create($billingData);
        }

        // then i need to set a flash message
        $request->session()->flash('flash_message','Your profile was successfully updated');

        //after i direct the user to the profile page
        return redirect()->route('profile');
    }

    public function passwordUpdate(PasswordUpdateRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $passwordData = $request->validated();
        $user->password = Hash::make($passwordData['new_password']);
        $user->save();

        // then i need to set a flash message
        $request->session()->flash('flash_message', 'Your password was successfully updated');
        return redirect()->route('profile');
    }
}
