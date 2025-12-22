@extends('layouts.vuexy')
@section('title', 'Translate')
@section('styles')
    <style>
        .table tr {
            border-width: 1px;
        }
    </style>
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row justify-content-end">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="search_key">{{__('search')}}</label>
                        <input type="text" class="form-control" id="search_key" placeholder="{{__('search')}}">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover ">
                <thead>
                <tr>
                    <th>{{__('key')}}</th>
                    @foreach($langKeys as  $value)
                        <th>{{$value}}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach($keys as $key)
                    <tr>
                        <td>{{$key}}</td>
                        @foreach($langKeys as  $value)
                            <td>
                                <input type="text" class="form-control"
                                       data-key="{{$key}}"  data-lang="{{$value}}"
                                       value="{{$data[$value][$key] ?? ''}}"
                                       name="lang[{{$value}}][{{$key}}]">
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
