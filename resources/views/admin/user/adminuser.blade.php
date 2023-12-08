
@extends('admin.layout.admin')
@section('content')
<div class="col-md-12 mt-2">

    <div class="card container-fluid">
        
        <div class="card-body w-100 ">
            <div class="d-flex container-fluid p-2">
                <h3>Master User</h3>
                <a  href="/admin/adduser" class="btn btn-primary ml-auto mb-1">Add</a>
            </div>
            <table id="myTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Profil</th>
                        <th>Username</th>
                        <th>Email</th>
                        {{-- <th>Role</th> --}}
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($listuser as $item)
                        
                    
                    <tr>
                        <th>{{$loop->iteration}}</th>
                        <th><img src="{{ Storage::url("photo/$item->user_profil") }}" alt="" width="100%" ></th>
                        <th>{{$item->user_name}}</th>
                        <th>{{$item->user_email}}</th>
                        {{-- <th>{{$item->user_role}}</th> --}}
                        <th><a href="{{route('viewEditUser',$item->user_id)}} " class="btn btn-success mr-1">Edit</a><a  href="#" class="btn btn-danger">Delete</a></th>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection