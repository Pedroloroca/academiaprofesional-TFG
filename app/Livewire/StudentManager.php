<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StudentManager extends Component
{
    public $student_id, $name, $email, $password, $date_of_birth, $address;
    public $isOpen = false;

    public function mount()
    {
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->student_id = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->date_of_birth = '';
        $this->address = '';
    }

    public function store()
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . ($this->student_id ? Student::find($this->student_id)->user_id : 'NULL'),
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
        ];

        if (!$this->student_id) {
            $rules['password'] = 'required|min:6';
        }

        $this->validate($rules);

        if ($this->student_id) {
            $student = Student::find($this->student_id);
            $user = $student->user;
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
            ]);
            if ($this->password) {
                $user->update(['password' => Hash::make($this->password)]);
            }
            $student->update([
                'date_of_birth' => $this->date_of_birth,
                'address' => $this->address,
            ]);
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);
            $user->assignRole('student');

            Student::create([
                'user_id' => $user->id,
                'date_of_birth' => $this->date_of_birth,
                'address' => $this->address,
            ]);
        }

        session()->flash('message', $this->student_id ? 'Estudiante actualizado.' : 'Estudiante creado.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $student = Student::findOrFail($id);
        $this->student_id = $id;
        $this->name = $student->user->name;
        $this->email = $student->user->email;
        $this->date_of_birth = $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : '';
        $this->address = $student->address;
        $this->password = '';
        $this->openModal();
    }

    public function delete($id)
    {
        $student = Student::find($id);
        if ($student && $student->user) {
            $student->user->delete();
        }
        session()->flash('message', 'Estudiante eliminado.');
    }

    public $search = '';

    public function render()
    {
        $query = Student::query();

        if (!empty($this->search)) {
            $query->whereHas('user', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.student-manager', [
            'students' => $query->with('user')->get(),
        ])->layout('layouts.livewire');
    }
}
