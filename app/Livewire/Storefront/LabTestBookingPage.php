<?php

namespace App\Livewire\Storefront;

use App\Livewire\Storefront\Concerns\HandlesRazorpayPayment;
use App\Models\LabCenter;
use App\Models\LabTest;
use App\Models\LabTestCategory;
use App\Services\LabTests\LabTestBookingService;
use Carbon\Carbon;
use Livewire\Component;

class LabTestBookingPage extends Component
{
    use HandlesRazorpayPayment;

    public string $step = 'test';

    public ?int $selectedTestId = null;

    public ?int $selectedCenterId = null;

    public string $scheduledDate = '';

    public ?string $errorMessage = null;

    public function selectTest(int $testId): void
    {
        $this->selectedTestId = $testId;
        $this->selectedCenterId = null;
        $this->step = 'center';
    }

    public function selectCenter(int $centerId): void
    {
        $this->selectedCenterId = $centerId;
        $this->step = 'date';
    }

    public function backTo(string $step): void
    {
        $this->step = $step;
        $this->errorMessage = null;
    }

    public function confirmBooking(LabTestBookingService $booking): void
    {
        $this->errorMessage = null;

        if (! auth('web')->check()) {
            $this->redirect(route('storefront.login', ['redirect' => url()->current()]), navigate: true);

            return;
        }

        if (! $this->scheduledDate) {
            $this->errorMessage = 'Please choose a date.';

            return;
        }

        $test = LabTest::findOrFail($this->selectedTestId);
        $center = LabCenter::findOrFail($this->selectedCenterId);
        $address = auth('web')->user()->addresses()->where('is_default', true)->first()
            ?? auth('web')->user()->addresses()->first();

        try {
            // book() expects a Collection - single-element here, since
            // this page books one test at a time.
            $order = $booking->book(auth('web')->user(), collect([$test]), $center, Carbon::parse($this->scheduledDate), $address?->id);
        } catch (\RuntimeException $e) {
            $this->errorMessage = $e->getMessage();

            return;
        }

        // book() above already ran the same payment.enabled bypass and
        // marked the order successful - calling initiatePayment() here
        // too would try to open the real Razorpay widget with
        // LogPaymentGateway's fake key for an order that's already
        // paid. Go straight to confirmation instead, same as a real
        // successful payment does.
        if (! config('services.payment.enabled')) {
            $this->redirect(route('storefront.orders.confirmation', $order->id), navigate: true);

            return;
        }

        $this->initiatePayment($order);
    }

    public function render()
    {
        $categories = LabTestCategory::where('is_active', true)
            ->with(['labTests' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        $selectedTest = $this->selectedTestId ? LabTest::find($this->selectedTestId) : null;

        $centers = collect();
        if ($selectedTest) {
            $centers = $selectedTest->centers()
                ->where('lab_centers.is_active', true)
                ->when(! $selectedTest->requires_center_visit, fn ($q) => $q->where('offers_home_collection', true))
                ->get();
        }

        return view('livewire.storefront.lab-test-booking-page', compact('categories', 'selectedTest', 'centers'))
            ->layout('components.layouts.storefront', ['title' => 'Book a Lab Test - Susthayan']);
    }
}
