<?php

namespace App\Livewire\Storefront;

use App\Livewire\Storefront\Concerns\HandlesRazorpayPayment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorHospitalAffiliation;
use App\Services\Appointments\AppointmentBookingService;
use Carbon\Carbon;
use Livewire\Component;

class AppointmentBookingPage extends Component
{
    use HandlesRazorpayPayment;

    public string $step = 'department';

    public ?int $selectedDepartmentId = null;

    public ?int $selectedDoctorId = null;

    public ?int $selectedAffiliationId = null;

    public string $scheduledDate = '';

    public ?string $errorMessage = null;

    public function selectDepartment(int $departmentId): void
    {
        $this->selectedDepartmentId = $departmentId;
        $this->selectedDoctorId = null;
        $this->step = 'doctor';
    }

    public function selectDoctor(int $doctorId): void
    {
        $this->selectedDoctorId = $doctorId;
        $this->selectedAffiliationId = null;
        $this->step = 'hospital';
    }

    public function selectAffiliation(int $affiliationId): void
    {
        $this->selectedAffiliationId = $affiliationId;
        $this->step = 'date';
    }

    public function backTo(string $step): void
    {
        $this->step = $step;
        $this->errorMessage = null;
    }

    public function confirmBooking(AppointmentBookingService $booking): void
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

        $doctor = Doctor::findOrFail($this->selectedDoctorId);
        $affiliation = DoctorHospitalAffiliation::findOrFail($this->selectedAffiliationId);

        try {
            $order = $booking->book(auth('web')->user(), $doctor, $affiliation, Carbon::parse($this->scheduledDate));
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
        $departments = Department::where('is_active', true)
            ->withCount(['doctors' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        $selectedDoctor = $this->selectedDoctorId ? Doctor::with('department')->find($this->selectedDoctorId) : null;

        $doctors = collect();
        if ($this->selectedDepartmentId && ! $selectedDoctor) {
            $doctors = Doctor::where('department_id', $this->selectedDepartmentId)->where('is_active', true)->get();
        }

        $affiliations = collect();
        if ($selectedDoctor) {
            $affiliations = $selectedDoctor->affiliations()->where('is_active', true)->with(['hospital', 'visitDays'])->get();
        }

        return view('livewire.storefront.appointment-booking-page', compact('departments', 'doctors', 'selectedDoctor', 'affiliations'))
            ->layout('components.layouts.storefront', ['title' => 'Book a Doctor - Susthayan']);
    }
}
