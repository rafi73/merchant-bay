<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'company_name' => $this->company_name,
            'company_email' => $this->company_email
        ];
    }

    public function with($request)
    {
        return [
            'version' => '1.0.0',
        ];
    }
}
