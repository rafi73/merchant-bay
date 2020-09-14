<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Storage;

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
            'image' => Storage::url($this->image),
            'code_category' => $this->codeCategory,
        ];
    }

    public function with($request)
    {
        return [
            'version' => '1.0.0',
        ];
    }
}
