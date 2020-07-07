<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ExternalBooksCollection extends ResourceCollection
{
    public $collects = ExternalBookResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'status_code' => 200,
            'status' => 'success',
            'data' => $this->collection,
        ];
    }
}
