<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Address\AddressResource;
use App\Http\Requests\Address\AddressCreateRequest;
use App\Http\Requests\Address\AddressUpdateRequest;
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

    public function get(string $idContact, string $idAddress)
    {
        $user = Auth::user();
        $contact = Contact::with('addresses')->where('user_id', $user->id)->where('id', $idContact)->first();
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $address = $contact->addresses()->where('id', $idAddress)->first();

        if (!$address) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }
        return new AddressResource($address);
    }

    public function update(AddressUpdateRequest $request, string $idContact, string $idAddress)
    {
        $data = $request->all();
        $user = Auth::user();
        $contact = Contact::with('addresses')->where('user_id', $user->id)->where('id', $idContact)->first();

        if (!$contact) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $address = $contact->addresses()->where('id', $idAddress)->first();
        if (!$address) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $address->fill($data);
        $address->save();

        return new AddressResource($address);
    }

    public function delete(string $idContact, string $idAddress)
    {
        $user = Auth::user();
        $contact = Contact::with('addresses')->where('user_id', $user->id)->where('id', $idContact)->first();

        if (!$contact) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $address = $contact->addresses()->where('id', $idAddress)->first();
        if (!$address) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $address->delete();

        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }

    public function getList(string $id)
    {
        $user = Auth::user();
        $contact = Contact::with('addresses')->where('user_id', $user->id)->where('id', $id)->first();

        if (!$contact) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $addresses = $contact->addresses;

        return AddressResource::collection($addresses);
    }
}
