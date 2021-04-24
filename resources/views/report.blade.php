<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <style>
      @import url("https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap");

      /* base styles */
      * {
        margin: 0;
        font-family: "Quicksand";
        color: #333;
      }
    </style>
</head>

<body>
<div class="mt-2">
        <h4 class="text-start mb-3">Users Report</h4>
 
        <table class="table table-sm table-striped mb-5">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Number of Tweets</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users ?? '' as $user)
                @php
                  $total_tweets += count($user->tweets)
                @endphp
                <tr>
                    <th scope="row">{{ $user->id }}</th>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ count($user->tweets) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <hr>
        <h5>Average number of tweets per user = {{ $total_tweets / $total_users }}</h5>
    </div>
</body>
</html>