<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class BasicResource extends JsonResource
{
    /** @var string */
    public static $wrap = 'data';

    public static string $wrapCollection = 'data';

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>|Arrayable<string, mixed>|\JsonSerializable
     */
    public function toArray(Request $request): array|Arrayable|\JsonSerializable
    {
        return parent::toArray($request);
    }
}
