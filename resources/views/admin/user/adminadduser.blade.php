
@extends('admin.layout.admin')
@section('content')

<div class="col-md-12 mt-2">

    <div class="card container-fluid">

        <div class="card-body w-100 ">
            <div class="d-flex container-fluid p-2">
                <h3>Add Master User</h3>
                <a href="/adminuser" class="btn btn-primary ml-auto mb-1">Back</a>
            </div>
            <form action="{{ route('Useraddadmin') }}" method="post"
                enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama"
                        placeholder="Masukkan Nama">
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                        placeholder="Masukkan Email">
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Masukkan Password">
                </div>

                <!-- Upload Foto -->
                <div class="mb-3">
                    <label for="foto" class="form-label">Upload Foto</label><br>
                    <input type="file" class="" id="foto" name="foto[]">
                </div>

                <div class="d-flex mb-3">
                    <div class="form-check mr-5">
                        <input class="form-check-input" type="radio" value="admin"
                            id="adminradio" name="admin">
                        <label class="form-check-label" for="adminradio">
                            Admin
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="user"
                            id="userradio" name="customer">
                        <label class="form-check-label" for="userradio">
                            User
                        </label>
                    </div>
                </div>
                <!-- Tombol Submit -->
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
@endsection