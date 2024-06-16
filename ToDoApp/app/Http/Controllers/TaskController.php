<?php
namespace App\Http\Controllers;

use App\Http\Requests\TodoRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TaskCompletedMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $todos = Task::where('user_id', $userId)->where('completed', false)->get();
        $completedTodos = Task::where('user_id', $userId)->where('completed', true)->get();
        return view('Task.index', ['todos' => $todos, 'completedTodos' => $completedTodos]);
    }

    public function store(TodoRequest $request)
    {
        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'completed' => false,
            'user_id' => Auth::id()
        ]);

        $request->session()->flash('alert-success', 'Task Created Successfully');
        return to_route('tasks.index');
    }

    public function show($id)
    {
        $todo = Task::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$todo) {
            session()->flash('error', 'Unable to locate the task');
            return to_route('tasks.index');
        }
        return view('Task.show', ['todo' => $todo]);
    }

    public function edit($id)
    {
        $todo = Task::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$todo) {
            session()->flash('error', 'Unable to locate the task');
            return to_route('tasks.index');
        }
        return view('Task.edit', ['todo' => $todo]);
    }

    public function update(TodoRequest $request)
    {
        $todo = Task::where('id', $request->todo_id)
                    ->where('user_id', Auth::id())
                    ->first();

        if (!$todo) {
            $request->session()->flash('error', 'Unable to locate the task');
            return to_route('tasks.index');
        }

        $todo->update([
            'title' => $request->title,
            'description' => $request->description,
            'completed' => $request->completed
        ]);

        $request->session()->flash('alert-info', 'Task Updated Successfully');
        return to_route('tasks.index');
    }

    public function destroy(Request $request)
    {
        $todo = Task::where('id', $request->todo_id)
                    ->where('user_id', Auth::id())
                    ->first();

        if (!$todo) {
            $request->session()->flash('error', 'Unable to locate the task');
            return to_route('tasks.index');
        }

        $todo->delete();
        $request->session()->flash('alert-success', 'Task Deleted Successfully');
        return to_route('tasks.index');
    }

    public function complete($id)
    {
        $todo = Task::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$todo) {
            session()->flash('error', 'Unable to locate the task');
            return redirect()->route('tasks.index');
        }

        $todo->completed = true;
        $todo->save();

        // Send the email
        try {
            Mail::to(Auth::user()->email)->send(new TaskCompletedMail($todo->title, $todo->description));
            Log::info('Email sent for completed task: ' . $todo->title);
        } catch (\Exception $e) {
            Log::error('Failed to send email: ' . $e->getMessage());
        }

        session()->flash('alert-success', 'Task marked as completed and email sent!');
        return redirect()->route('tasks.completed');
    }

    public function completed()
    {
        $completedTodos = Task::where('user_id', Auth::id())->where('completed', true)->get();
        return view('Task.completed', ['completedTodos' => $completedTodos]);
    }
}
