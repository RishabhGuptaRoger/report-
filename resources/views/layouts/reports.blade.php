<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Integrations Report</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>    @livewireStyles
    <style>
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown button {
            background-color: #29d  ;
            border: none;
            cursor: pointer;
        }

        .dropdown .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            display: none;
            float: left;
            min-width: 10rem;
            padding: 0.5rem 0;
            margin: 0.125rem 0 0;
            font-size: 1rem;
            text-align: left;
            list-style: none;
            background-color: #fff;
            border: 1px solid rgba(0, 0, 0, 0.125);
            border-radius: 0.25rem;
        }

        .dropdown .dropdown-menu.show {
            display: block;
        }

        .dropdown .dropdown-item {
            display: block;
            width: 100%;
            padding: 0.25rem 1.5rem;
            clear: both;
            font-weight: 400;
            color: #212529;
            text-align: inherit;
            white-space: nowrap;
            background-color: transparent;
            border: 0;
            font-weight: 500;
            color: #333;
            text-decoration: none;
        }

        .dropdown .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .dropdown .btn-primary {
            background-color: #3490dc;
            color: #fff;
            border: none;
            cursor: pointer;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            border-radius: 0.25rem;
            text-decoration: none;
        }

        .dropdown .btn-primary:hover {
            background-color: #2779bd;
        }
    </style>
</head>
<body>

@auth
    <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown">
            {{ auth()->user()->name }}
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('profile.show') }}">Profile</a></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="dropdown-item" type="submit">Log Out</button>
                </form>
            </li>
        </ul>
    </div>
@else
    <a href="{{ route('login') }}" class="btn btn-primary">Log in</a>
    <a href="{{ route('register') }}" class="btn btn-primary ml-2">Register</a>
@endauth


<livewire:report-page />

@livewireScripts

</body>
</html>
