<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function create(ContactCreateRequest $request)
    {
        $data = $request->validated();

        $user = Auth::user();

        $contact = new Contact($data);
        $contact->user_id = $user->id;
        $contact->save();

        return (new ContactResource($contact))->response()->setStatusCode(201);
    }

    public function get($id)
    {
        $user = Auth::user();

        $contact = Contact::where("id", $id)->where("user_id", $user->id)->first();

        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "contact not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        return new ContactResource($contact);
    }

    public function update($id, ContactUpdateRequest $request)
    {

        $user = Auth::user();

        $contact = Contact::where("id", $id)->where("user_id", $user->id)->first();

        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "contact not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $data = $request->validated();

        $contact ->update($data);

        return new ContactResource($contact);
    }

    public function delete($id)
    {
        $user = Auth::user();

        $contact = Contact::where("id", $id)->where("user_id", $user->id)->first();

        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "contact not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $contact->delete();

        return response()->json([
            "message" => "contact deleted"
        ])->setStatusCode(200);
    }
}
