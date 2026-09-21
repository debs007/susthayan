<?php

namespace App\Livewire\Storefront;

use App\Models\HealthRecord;
use App\Models\Prescription;
use App\Models\Vital;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class HealthRecordsPage extends Component
{
    use WithFileUploads;

    #[Validate('required|file|mimes:jpg,jpeg,png,pdf|max:10240')]
    public $prescriptionFile = null;

    public ?string $uploadMessage = null;

    /** Same validation and private-disk storage as PrescriptionController::store() in the API - never a public URL. */
    public function uploadPrescription(): void
    {
        $this->validate();

        $path = $this->prescriptionFile->store('prescriptions/'.auth('web')->id(), 'local');

        Prescription::create([
            'user_id' => auth('web')->id(),
            'file_path' => $path,
            'verification_status' => 'pending',
        ]);

        $this->reset('prescriptionFile');
        $this->uploadMessage = 'Uploaded - a pharmacist will review it shortly.';
    }

    public function render()
    {
        $userId = auth('web')->id();

        $vitals = Vital::where('user_id', $userId)->latest('recorded_at')->limit(20)->get();
        $records = HealthRecord::where('user_id', $userId)->latest('record_date')->limit(20)->get();
        $prescriptions = Prescription::where('user_id', $userId)->latest()->limit(20)->get();

        return view('livewire.storefront.health-records-page', compact('vitals', 'records', 'prescriptions'))
            ->layout('components.layouts.storefront', ['title' => 'Health Records - Susthayan']);
    }
}
