<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Address\AddressResource;
use App\Http\Requests\Address\AddressCreateRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddressController extends Controller
{
    public function create(AddressCreateRequest $request, int $id)
    {
        $data = $request->all();
        $user = Auth::user();

        $contactsForCurrentUser = Contact::where('user_id', $user->id)->where('id', $id)->first();

        if (!$contactsForCurrentUser) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $address = new Address($data);
        $address->contact_id = $contactsForCurrentUser->id;
        $address->save();

        return new AddressResource($address);
    }
}