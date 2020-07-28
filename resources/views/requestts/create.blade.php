@extends('layouts.app')

@section('stylesheets')
  <link href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.2.0/trix.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
    <div class="card card-default">
        <div class="card-header">
            {{ isset($requestt) ? "Update request" : "make request" }}
        </div>
        <div class="card-body">
        <form action="{{ isset($requestt) ? route('requestts.update', $requestt->id) : route('requestts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if (isset($requestt))
                  @method('PUT')
                @endif
                <div class="form-group">
                    <label for="meetpoint">Meetpoint:</label>
                <input type="text" class="form-control" name="meetpoint" placeholder="Enter the meetpoint" value="{{ isset($requestt) ? $requestt->meetpoint: ''}}">
                </div>
                <div class="form-group">
                    <label for="Destination">Destination:</label>
                    <input type="text" class="form-control" name="destination" placeholder="Enter your destination" value="{{ isset($requestt) ? $requestt->destination: ''}}">
                </div>
                <div class="form-group">
                    <label for="Time">Time:</label>
                    <input type="time" class="form-control" name="time" value="{{ isset($requestt) ? $requestt->time: ''}}">
                </div>
                <div class="form-group">
                    <label for="neededSeats">Needed seats:</label>
                    <input type="number" class="form-control" name="neededSeats" min="1" max="4" placeholder="Enter number of seats you need" value="{{ isset($requestt) ? $requestt->neededSeats: ''}}">
                </div>
                <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                        {{ isset($requestt) ? "Update" : "Add" }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.2.0/trix.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/js/select2.min.js"></script>

    <script>
      $(document).ready(function() {
        $('.tags').select2();
      });
    </script>
@endsection
