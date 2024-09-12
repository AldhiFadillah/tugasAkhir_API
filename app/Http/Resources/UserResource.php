<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Collection;

class UserResource extends JsonResource
{
    //define properti
    public $status;
    public $message;
    public $collection;
    public $resource;

    /**
     * __construct
     *
     * @param  mixed $status
     * @param  mixed $message
     * @param  mixed $resource
     * @return void
     */
    public function __construct($status, $message, $collection, $resource)
    {
        parent::__construct($resource);
        $this->status  = $status;
        $this->collection = $collection;
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
        // Check if the resource is a collection or a single object
        if ($this->collection){
            $encryptedData = collect($this->resource)->map(function($user) {
                return [
                    'id' => isset($user['id']) ? Crypt::encryptString($user['id']) : null,
                    'username' => $user['username'] ?? null,
                    'email' => $user['email'] ?? null,
                    'refresh_count' => $user['refresh_count'] ?? null,
                    'role' => $user['role'] ?? null,
                ];
            });
        } else {
            // For single object
            $encryptedData = $this->resource;
        }

        return [
            'success'   => $this->status,
            'message'   => $this->message,
            // 'data'      => $this->resource
            'data'      => $encryptedData
        ];
    }
}
