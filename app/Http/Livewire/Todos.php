<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Todo;
use Livewire\WithFileUploads;

class Todos extends Component
{
    use WithFileUploads;
    public $todos, $title, $image, $description, $todo_id;
    public $isOpen = 0;

    public function render()
    {
        $this->todos = Todo::orderBy('id', 'DESC')->get();
        return view('livewire.todo.todos');
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
    }

    private function resetInputFields()
    {
        $this->title = '';
        $this->image = '';
        $this->description = '';
    }

    public function store()
    {
        $this->validate([
            'title' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required'
        ]);

        Todo::updateOrCreate(['id' => $this->todo_id], [
            'title' => $this->title,
            'image' => $this->image->store('assets/todo', 'public'),
            'description' => $this->description
        ]);

        //BUAT FLASH SESSION UNTUK MENAMPILKAN ALERT NOTIFIKASI
        session()->flash('message', $this->todo_id ? $this->title . ' Diperbaharui' : $this->title . ' Ditambahkan');
        $this->closeModal(); //TUTUP MODAL
        $this->resetInputFields(); //DAN BERSIHKAN FIELD
    }

    public function edit($id)
    {
        $todo = Todo::findOrFail($id);
        $this->todo_id = $id;
        $this->title = $todo->title;
        $this->image = $todo->image;
        $this->description = $todo->description;

        $this->openModal();
    }

    public function delete($id)
    {
        $todo = Todo::find($id); //BUAT QUERY UNTUK MENGAMBIL DATA BERDASARKAN ID
        $todo->delete(); //LALU HAPUS DATA
        session()->flash('message', $todo->title . ' Dihapus'); //DAN BUAT FLASH MESSAGE UNTUK NOTIFIKASI
    }
}
