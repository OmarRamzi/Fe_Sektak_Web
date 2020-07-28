@extends('layouts.app')

@section('content')
  <div class="clearfix">
    <a href="{{ route('requestts.create') }}"
    class="btn float-right btn-success"
    style="margin-bottom: 10px">
      Make Request
    </a>
  </div>

  <div class="card card-default">
    <div class="card-header">My Requests</div>
        @if ($requestts->count() > 0)
          <table class="card-body">
            <table class="table">
              <thead>
                <tr>
                  <th>Meetpoint</th>
                  <th>Destination</th>
                  <th>Time</th>
                  <th>NeededSeets</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($requestts as $requestt)
                  <tr>
                    <td>
                      {{$requestt->meetpoint  }}
                    </td>
                    <td>
                      {{ $requestt->destination }}
                    </td>
                    <td>
                        {{ $requestt->time }}
                    </td>
                    <td>
                        <span class="ml-2 badge badge-primary">{{ $requestt->neededSeats }}</span>
                    </td>

                    <td>
                      <form class="float-right ml-2"
                      action="{{route('requestts.destroy', $requestt->id)}}" method="POST">
                        @csrf
                        @method('DELETE')
                          <button class="btn btn-danger btn-sm">
                            Delete
                        </button>
                      </form>
                        <a href="{{route('requestts.edit', $requestt->id)}}" class="btn btn-primary float-right btn-sm">Edit</a>
                        <a href="{{route('requestts.viewAvailableRides', $requestt->id)}}" class="btn btn-primary float-right btn-sm" style="margin-right:3%; ">View available rides</a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
          </table>
        @else
          <div class="card-body">
            <h1 class="text-center">
               No Requests Yet
            </h1>
          </div>
        @endif
    </div>
</div>


@endsection
