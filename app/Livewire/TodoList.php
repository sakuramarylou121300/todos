<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Rule;
use App\Models\Todo;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;
    #[Rule('required| min:3| max:50')]
    public $name;

    public $search;

    public $editingTodoID;

    #[Rule('required| min:3| max:50')]
    public $editingTodoName;

    public function create(){
        // validate
        $validated = $this->validateOnly('name');
        Todo::create($validated);
        $this->reset('name');
        session()->flash('success', 'Created');
        // create the todo
        // create the input
        // send flash message
        $this->resetPage();
        
    }

    public function delete($todoID){
        try{
            Todo::findOrfail($todoID)->delete();
        }catch(Exception $e){
            session()->flash('error', 'Failed to delete todo!');
            return;
        }
    }

    // THIS IS TO CHECK IF THE TASK IS ALREADY DONE OR NOT
    public function toggle($todoID){
        $todo = Todo::find($todoID);
        $todo->completed = !$todo->completed;
        $todo->save();
    }

    public function edit($todoID){
        $this->editingTodoID = $todoID;
        $this->editingTodoName = Todo::find($todoID)->name;
    }

    // exit the edit page if cancelled is clicked
    public function cancelEdit(){
        $this->reset('editingTodoID', 'editingTodoName');
    }

    public function update(){
        $this->validateOnly('editingTodoName');
        Todo::find($this->editingTodoID)->update([
            'name'=>$this->editingTodoName
        ]);

        $this->cancelEdit();
    }

    public function render()
    {
        return view('livewire.todo-list', [
            'todos'=> Todo::latest()->where('name', 'like', "%{$this->search}%")->paginate(5)
        ]);
    }
}
