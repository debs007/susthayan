<?php

namespace App\Livewire\Admin;

use App\Models\Prescription;
use App\Models\Product;
use App\Services\Orders\CheckoutService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use RuntimeException;

#[Layout('components.layouts.app')]
class CreateOrderFromPrescription extends Component
{
    #[Locked]
    public int $prescriptionId;

    /**
     * One entry per line: medicine_name (the pharmacist's transcription,
     * read-only reference), product_search (what's typed into that
     * line's search box), product_id/product_name (once matched -
     * product_id null means not yet matched), quantity, results (the
     * current search's matches for that line only).
     */
    public array $lines = [];

    public string $fulfillmentType = 'pickup';

    // Either an existing address's id (as a string, to match <select>
    // option values) or the literal "new" - picking "new" reveals the
    // inline address form below instead of using a saved address.
    public string $selectedAddressOption = '';
    public string $newAddressLabel = '';
    public string $newAddressLine1 = '';
    public string $newAddressLine2 = '';
    public string $newAddressCity = '';
    public string $newAddressState = '';
    public string $newAddressPincode = '';

    public ?string $errorMessage = null;

    public function mount(Prescription $prescription): void
    {
        abort_unless($prescription->verification_status === 'approved', 404, 'This prescription has not been approved.');
        abort_if($prescription->order_id !== null, 404, 'An order has already been created from this prescription.');

        $this->prescriptionId = $prescription->id;
        $defaultAddressId = $prescription->user->addresses()->where('is_default', true)->value('id')
            ?? $prescription->user->addresses()->value('id');
        $this->selectedAddressOption = $defaultAddressId ? (string) $defaultAddressId : 'new';

        $medicines = $prescription->medicines;
        $this->lines = $medicines->isNotEmpty()
            ? $medicines->map(fn ($m) => $this->emptyLine($m->medicine_name))->all()
            : [$this->emptyLine('')];
    }

    private function emptyLine(string $medicineName): array
    {
        return [
            'medicine_name' => $medicineName,
            'product_search' => '',
            'product_id' => null,
            'product_name' => null,
            'quantity' => 1,
            'results' => [],
        ];
    }

    public function addLine(): void
    {
        $this->lines[] = $this->emptyLine('');
    }

    public function removeLine(int $index): void
    {
        unset($this->lines[$index]);
        $this->lines = array_values($this->lines);
    }

    /** Called directly from the view on each keystroke (debounced there) rather than relying on an implicit updated{Property} hook. */
    public function searchProduct(int $index): void
    {
        $term = $this->lines[$index]['product_search'] ?? '';

        $this->lines[$index]['results'] = strlen($term) < 2
            ? []
            : Product::where('is_active', true)
                ->where('name', 'like', "%{$term}%")
                ->orderBy('name')
                ->limit(8)
                ->get(['id', 'name'])
                ->all();
    }

    public function selectProduct(int $index, int $productId, string $productName): void
    {
        $this->lines[$index]['product_id'] = $productId;
        $this->lines[$index]['product_name'] = $productName;
        $this->lines[$index]['product_search'] = '';
        $this->lines[$index]['results'] = [];
    }

    public function clearProduct(int $index): void
    {
        $this->lines[$index]['product_id'] = null;
        $this->lines[$index]['product_name'] = null;
    }

    public function submit(CheckoutService $checkout): void
    {
        $this->errorMessage = null;

        $matched = collect($this->lines)->filter(fn ($l) => $l['product_id'] !== null);

        if ($matched->isEmpty()) {
            $this->errorMessage = 'Match at least one line to a product before creating the order.';
            return;
        }

        $items = $matched->map(fn ($l) => [
            'product_id' => $l['product_id'],
            'quantity' => max(1, (int) $l['quantity']),
        ])->all();

        $prescription = Prescription::with('user')->findOrFail($this->prescriptionId);

        $addressId = null;
        if ($this->fulfillmentType === 'delivery') {
            if ($this->selectedAddressOption === 'new') {
                if (trim($this->newAddressLine1) === '' || trim($this->newAddressCity) === ''
                    || trim($this->newAddressState) === '' || trim($this->newAddressPincode) === '') {
                    $this->errorMessage = 'Fill in the address line, city, state, and pincode.';
                    return;
                }

                $addressId = $prescription->user->addresses()->create([
                    'label' => $this->newAddressLabel ?: null,
                    'line1' => $this->newAddressLine1,
                    'line2' => $this->newAddressLine2 ?: null,
                    'city' => $this->newAddressCity,
                    'state' => $this->newAddressState,
                    'pincode' => $this->newAddressPincode,
                    'is_default' => false,
                ])->id;
            } else {
                $addressId = (int) $this->selectedAddressOption;
            }
        }

        try {
            $order = $checkout->placeOrderFromPrescription(
                user: $prescription->user,
                prescription: $prescription,
                items: $items,
                fulfillmentType: $this->fulfillmentType,
                addressId: $addressId,
            );
        } catch (RuntimeException $e) {
            $this->errorMessage = $e->getMessage();
            return;
        }

        $this->redirect(route('admin.orders.show', $order), navigate: false);
    }

    public function render()
    {
        $prescription = Prescription::with('user.addresses')->findOrFail($this->prescriptionId);

        return view('livewire.admin.create-order-from-prescription', compact('prescription'));
    }
}
