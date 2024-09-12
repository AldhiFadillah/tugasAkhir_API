<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;

class UserResource extends JsonResource
{
    //define properti
    public $status;
    public $message;
    public $resource;

    /**
     * __construct
     *
     * @param  mixed $status
     * @param  mixed $message
     * @param  mixed $resource
     * @return void
     */
    public function __construct($status, $message, $resource)
    {
        parent::__construct($resource);
        $this->status  = $status;
        $this->message = $message;
    }

    /**
     * toArray
     *
     * @param  mixed $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        // Encrypt ID for each user
        // $encryptedData = $this->resource->map(function($user) {
        //     return [
        //         // 'id' => Crypt::encryptString($user->id),
        //         'username' => $user->username,
        //         'email' => $user->email,
        //         'refresh_count' => $user->refresh_count,
        //         'role' => $user->role,
        //         // Add other fields as necessary
        //     ];
        // });

        return [
            'success'   => $this->status,
            'message'   => $this->message,
            'data'      => $this->resource
        ];
    }
}
