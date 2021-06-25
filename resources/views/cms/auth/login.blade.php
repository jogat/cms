

@extends('layouts.cms')
@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header"><h3 class="text-center font-weight-light my-4">Login</h3></div>

                @if($errors->any())
                    <h4>{{$errors->first()}}</h4>
                @endif
                <div class="card-body">
                    <form method="post" action="{{url('login')}}">
                        @csrf
                        <input type="hidden" name="next" value="{{$next}}">
                        <div class="form-group">
                            <label class="small mb-1" for="inputEmailAddress">Email</label>
                            <input class="form-control" id="inputEmailAddress" type="email" name="email" placeholder="Enter email address" value="josue@mail.com" />
                        </div>
                        <div class="form-group">
                            <label class="small mb-1" for="inputPassword">Password</label>
                            <input class="form-control" id="inputPassword" type="password" name="password" placeholder="Enter password" value="Password123$"/>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" id="rememberPasswordCheck" type="checkbox" />
                                <label class="custom-control-label" for="rememberPasswordCheck">Remember password</label>
                            </div>
                        </div>
                        <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                            <a class="small" href="password.html">Forgot Password?</a>
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <div class="small"><a href="register.html">Need an account? Sign up!</a></div>
                </div>
            </div>
        </div>
    </div>

@endsection

