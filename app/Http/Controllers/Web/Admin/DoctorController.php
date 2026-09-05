<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateDoctorRequest;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorHospitalAffiliation;
use App\Models\Hospital;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DoctorController extends Controller
{
    public function index(): View
    {
        $doctors = Doctor::with('department', 'hospitals')->orderBy('name')->get();

        return view('admin.doctors.index', compact('doctors'));
    }

    public function create(): View
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $hospitals = Hospital::where('is_active', true)->orderBy('name')->get();

        return view('admin.doctors.create', compact('departments', 'hospitals'));
    }

    public function store(CreateDoctorRequest $request): RedirectResponse
    {
        $doctor = Doctor::create([
            ...$request->safe()->except(['photo', 'hospitals']),
            'photo_path' => $request->hasFile('photo') ? $request->file('photo')->store('doctors', 'r2') : null,
            'is_active' => true,
        ]);

        $this->syncHospitalAffiliations($doctor, $request->selectedHospitals());

        return redirect()->route('admin.doctors.edit', $doctor)->with('success', "Dr. {$doctor->name} was added.");
    }

    public function edit(Doctor $doctor): View
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $hospitals = Hospital::where('is_active', true)->orderBy('name')->get();
        $doctor->load('affiliations.visitDays');

        return view('admin.doctors.edit', compact('doctor', 'departments', 'hospitals'));
    }

    public function update(CreateDoctorRequest $request, Doctor $doctor): RedirectResponse
    {
        $doctor->update([
            ...$request->safe()->except(['photo', 'hospitals']),
            'photo_path' => $request->hasFile('photo') ? $request->file('photo')->store('doctors', 'r2') : $doctor->photo_path,
        ]);

        $this->syncHospitalAffiliations($doctor, $request->selectedHospitals());

        return redirect()->route('admin.doctors.edit', $doctor)->with('success', 'Saved.');
    }

    public function toggleActive(Doctor $doctor): RedirectResponse
    {
        $doctor->update(['is_active' => ! $doctor->is_active]);

        return back()->with('success', $doctor->is_active ? "Dr. {$doctor->name} is now active." : "Dr. {$doctor->name} is now hidden.");
    }

    /**
     * Replaces every affiliation + its visit days with exactly what was
     * submitted - a hospital unchecked this time around means its
     * affiliation (and schedule) is genuinely removed, not left stale.
     */
    private function syncHospitalAffiliations(Doctor $doctor, array $selectedHospitals): void
    {
        $doctor->affiliations()->delete(); // cascades to visitDays via the FK's cascadeOnDelete

        foreach ($selectedHospitals as $hospitalId => $data) {
            $affiliation = DoctorHospitalAffiliation::create([
                'doctor_id' => $doctor->id,
                'hospital_id' => $hospitalId,
                'consultation_charge' => $data['charge'],
                'is_active' => true,
            ]);

            foreach ($data['days'] as $day) {
                $affiliation->visitDays()->create([
                    'day_of_week' => $day,
                    'start_time' => $data['start_time'],
                    'end_time' => $data['end_time'],
                ]);
            }
        }
    }
}
