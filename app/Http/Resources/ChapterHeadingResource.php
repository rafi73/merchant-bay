<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChapterHeadingResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'code_category_id' => $this->code_category_id,
            'image' => $this->image,
        ];
    }

    public function with($request)
    {
        return [
            'version' => '1.0.0',
        ];
    }
}