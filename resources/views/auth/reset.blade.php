@extends('layouts/app')

{{-- Web site Title --}}
@section('title') Reset Password @stop

{{-- Content --}}
@section('content')
  <div class="row">
    <div class="page-header">
      <h2>Reset Password</h2>
    </div>
  </div>

  <div class="container-fluid">
    <div class="row">
      @include('errors.messages')
      <div class="col-md-8 col-md-offset-2">
        @include('errors.list')
        <div class="panel panel-default">
          <div class="panel-heading">Reset Password</div>
          <div class="panel-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ URL::to('/password/reset') }}">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <input type="hidden" name="token" value="{{ $token }}">
              <div class="form-group">
                <label class="col-md-4 control-label">Password</label>
                <div class="col-md-6">
                  <input type="password" class="form-control" name="password">
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label">Confirm Password</label>
                <div class="col-md-6">
                  <input type="password" class="form-control" name="password_confirmation">
                </div>
              </div>
              <div class="form-group">
                <div class="col-md-6 col-md-offset-4">
                  <button type="submit" class="btn btn-primary">Reset Password</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection