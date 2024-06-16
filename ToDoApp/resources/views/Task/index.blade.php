<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>To Do List</title>

    <!-- Include the Indie Flower font from Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Indie+Flower&display=swap">

    <style>
        body {
            background-color: #FFF9D0;
            font-family: 'Indie Flower', cursive;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #5AB2FF;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .table th {
            background-color: #f2f2f2;
        }

        .table th:first-child,
        .table td:first-child {
            border-left: none;
        }

        .table th:last-child,
        .table td:last-child {
            border-right: none;
        }

        .btn-container {
            display: flex;
            justify-content: center;
        }

        .btn {
            margin-right: 5px;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-success {
            background-color: #5AB2FF;
            color: white;
        }

        .btn-danger {
            background-color: #FF6347;
            color: white;
        }

        .btn-info {
            background-color: #A9A9A9;
            color: white;
        }

        .btn-info:hover {
            background-color: #4584d4;
        }

        .create-task-link {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .completed-task {
            color: green;
            font-weight: bold;
        }

        .check-icon {
            color: green;
            cursor: pointer;
            font-size: 20px;
        }

        .description-cell {
            word-wrap: break-word;
            white-space: normal;
            max-width: 300px;
        }
    </style>
</head>
<body>
    <header>
        <h1>To Do List</h1>
    </header>

    <div class="container">
        <div class="create-task-link">
            <a class="btn btn-sm btn-info" href="{{ route('dashboard') }}">Back</a>
            <a class="btn btn-sm btn-info" href="{{ route('tasks.completed') }}">Show Completed Tasks</a>
        </div>

        @if (Session::has('alert-success'))
            <div class="success-message">
                {{ Session::get('alert-success') }}
            </div>
        @endif

        @if (Session::has('error'))
            <div class="alert alert-danger" role="alert">
                {{ Session::get('error') }}
            </div>
        @endif

        @if (Session::has('alert-info'))
            <div class="alert alert-info" role="alert">
                {{ Session::get('alert-info') }}
            </div>
        @endif

        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Completed</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($todos as $todo)
                    <tr>
                        <td class="{{ $todo->completed ? 'completed-task' : '' }}">{{ $todo->title }}</td>
                        <td class="description-cell {{ $todo->completed ? 'completed-task' : '' }}">{{ $todo->description }}</td>
                        <td>
                            @if ($todo->completed)
                                <span class="check-icon">✔</span>
                            @else
                                <form method="post" action="{{ route('tasks.complete', $todo->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success check-icon">✔</button>
                                </form>
                            @endif
                        </td>
                        
                        <td class="btn-container">
                            <a class="btn btn-info" href="{{ route('tasks.edit', $todo->id) }}">Edit</a>
                            <form method="post" action="{{ route('tasks.destroy') }}">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="todo_id" value="{{ $todo->id }}">
                                <input class="btn btn-danger" type="submit" value="Delete">
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if ($todos->isEmpty())
            <h4>No Tasks Created Yet</h4>
        @endif
    </div>
</body>
</html>
