<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Contact\ContactResource;
use App\Http\Requests\Contact\ContactCreateRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ContactController extends Controller
{
    public function create(ContactCreateRequest $request)
    {
        $data = $request->all();
        $user = Auth::user();

        $contact = new Contact($data);
        $contact->user_id = $user->id;
        $contact->save();

        return (new ContactResource($contact))->response()->setStatusCode(201);
    }

    public function get(string $id)
    {
        $user = Auth::user();
        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }
        return new ContactResource($contact);
    }
}
