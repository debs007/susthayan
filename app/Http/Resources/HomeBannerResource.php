<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeBannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'image_url' => $this->image_url,
            'coupon_id' => $this->coupon_id,
            'badge_text' => $this->badge_text,
            'headline' => $this->headline,
            'subtitle' => $this->subtitle,
            'button_text' => $this->button_text,
        ];
    }
}
