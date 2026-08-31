<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Product
 *
 * Expects the controller to have already attached ->resolved_price (a
 * ProductPrice|null) and ->resolved_stock (int|null) onto each model
 * before wrapping it here - both need a franchise context to resolve and
 * are cheaper to batch-compute once per listing than to re-query per item
 * inside the resource (see ProductController).
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var \App\Models\ProductPrice|null $price */
        $price = $this->resolved_price ?? null;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'salt_composition' => $this->salt_composition,
            'manufacturer' => $this->manufacturer,
            'category' => $this->whenLoaded('category', fn () => $this->category?->name),
            'unit' => $this->unit,
            'prescription_required' => $this->prescription_required,
            'drug_schedule' => $this->drug_schedule,
            'description' => $this->description,
            'price' => $price ? [
                'mrp' => (string) $price->mrp,
                'selling_price' => (string) $price->selling_price,
                'tax_percentage' => (string) $price->tax_percentage,
            ] : null,
            'available_stock' => $this->resolved_stock,
            'in_stock' => ($this->resolved_stock ?? 0) > 0,
        ];
    }
}
