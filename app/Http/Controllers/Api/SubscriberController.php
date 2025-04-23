<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|unique:subscribers,email',
        ]);

        $storeSubscriber =  Subscriber::create($data);
        if ($storeSubscriber) {

            return ApiResponse::ResponseMessage($storeSubscriber, 'Subscriber Created Successfully', 201);
        }
        return ApiResponse::ResponseMessage([], 'Subscriber Not created ', 200);
    }
}
