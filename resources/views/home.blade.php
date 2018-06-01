@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div contenteditable="true" class="panel-heading">Measurements</div>

                    <div class="panel-body table-responsive">

                        {{--<router-view name="measurementsIndex"></router-view>--}}
                        <router-view :user="{{ Auth::user()->toJson() }}"></router-view>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
