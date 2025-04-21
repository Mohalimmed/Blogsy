<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ContactRequest;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ContactRequest $request)
    {
        $data = $request->validated();
        $ContactStore = Contact::create($data);
        if ($ContactStore) {
            return ApiResponse::ResponseMessage($data, 'Contact Created Successfully', 201);
        }
        return ApiResponse::ResponseMessage($data, 'Contact Not Created', 200);
    }
}
