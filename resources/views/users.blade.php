@extends('layouts.app')

@section('content')
    <style>
        .table thead>tr>td.vert-align {
            vertical-align: middle;
        }

        .table thead>tr>td.center {
            text-align: center;
        }

        .table tbody>tr>td.center {
            text-align: center;
        }

    </style>

    <script>

        function chkdelete(id, name) {

            var html =
                "<center><button type='button' id='confirmationRevertYes' class='btn btn-primary'>Ναί</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' id='confirmationRevertNo' class='btn btn-primary'>Όχι</button></center>";

            var msg =
                '<center><h4>Διαγραφή ?</h4><hr>Διαγραφή χρήστη ' +
                escapeHtml(name) +
                '. Είστε σίγουροι;<br>&nbsp;</center>';

            var $toast = toastr.warning(html, msg);

            $toast.delegate('#confirmationRevertYes', 'click', function() {
                deleteRecord(id, "{{ url('/users/del') }}");
                $toast.remove();
            });

            $toast.delegate('#confirmationRevertNo', 'click', function() {
                $toast.remove();
            });
        }




    </script>

    <div class="container">
        <div class="row">
            <div class="col-md-12 col-md-offset-0">
                <div class="panel panel-default">
                    <div class="panel-heading h1 text-center">Χρήστες</div>

                    <div class="panel-body">
                        <div class="panel panel-default col-md-12 col-sm-12  ">

                            <form name="myForm" class="form-horizontal" role="form" method="POST"
                                action="{{ url('/users') }}{{ $user->id ? '/' . $user->id : '' }}">
                                {{ csrf_field() }}

                                @if ($users->hasPages())
                                    <div class='row'>
                                        <div class="col-md-12 col-sm-12 small text-center">
                                            <span class="small">{{ $users->links() }}</span>
                                        </div>
                                    </div>
                                @endif


                                <div class='row'>

                                    <div class='col-md-4 col-sm-4 h4'>
                                        <div class='row'>
                                            <div class='col-md-7 col-sm-7'>
                                                <strong>Ονοματεπώνυμο</strong>
                                            </div>
                                            <div class='col-md-5 col-sm-5 '>
                                                <strong>Username</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-md-2 col-sm-2  h4'>
                                        <strong>E-mail</strong>
                                    </div>
                                    <div class='col-md-3 col-sm-3 h4  text-center'>
                                        <strong>Password</strong>
                                    </div>
                                    <div class='col-md-1 col-sm-1  h4'>
                                        <strong>Ρόλος</strong>
                                    </div>
                                    <div class='col-md-2 col-sm-2  h4'>
                                        <strong>Ενεργός</strong>
                                    </div>
                                </div>

                                <div class='row'>
                                    <div class='col-md-4 col-sm-4'>
                                        <div class='row'>
                                            <div class="col-md-7 col-sm-7 {{ $errors->has('name') ? ' has-error' : '' }}">
                                                <input id="name" type="text" class="form-control" name="name"
                                                    placeholder="name"
                                                    value="{{ old('name') ? old('name') : $user->name }}" required
                                                    autofocus>
                                            </div>
                                            <div
                                                class="col-md-5 col-sm-5 {{ $errors->has('username') ? ' has-error' : '' }}">
                                                <input id="username" type="text" class="form-control" name="username"
                                                    placeholder="username"
                                                    value="{{ old('username') ? old('username') : $user->username }}"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-2 {{ $errors->has('email') ? ' has-error' : '' }}">
                                        <input id="email" type="email" class="form-control" name="email"
                                            placeholder="email" value="{{ old('email') ? old('email') : $user->email }}"
                                            required>
                                    </div>
                                    <div class="col-md-3 col-sm-3 {{ $errors->has('password') ? ' has-error' : '' }}">
                                        <div class='row'>
                                            <div class="col-md-6 col-sm-6">
                                                <input id="password" type="password" class="form-control col-md-1 col-sm-1"
                                                    name="password" placeholder="passwd" {{ $user->id ? '' : 'required' }}>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <input id="password-confirmation" type="password"
                                                    class="form-control  col-md-1 col-sm-1" name="password_confirmation"
                                                    placeholder="confirm" {{ $user->id ? '' : 'required' }}>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-3 ">
                                        <div class='row'>
                                            <div class="col-md-5 col-sm-5 {{ $errors->has('role_id') ? ' has-error' : '' }}">
                                                <select id="role_id" class="form-control" name="role_id" required>
                                                    <option value=""></option>
                                                    @foreach ($roles as $role)
                                                        <option value="{{ $role->id }}"
                                                            {{ (string) old('role_id', $user->role_id) === (string) $role->id ? 'selected' : '' }}>
                                                            {{ $role->role }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2 col-sm-2 text-center">
                                                <input type="checkbox" class="form-control" id="active" name="active"
                                                    @if ($user->active) checked @endif
                                                    @if (!$user->id) checked @endif >
                                            </div>
                                            <div class="col-md-5 col-sm-5 text-center">
                                                <a href="javascript:document.forms['myForm'].submit();"
                                                    class="{{ $submitVisible }}" role="button" title="Αποθήκευση"> <img
                                                        src="{{ URL::to('/') }}/images/save.ico" height="30" /></a>
                                                <a href="{{ URL::to('/') }}/users" class="active" role="button"
                                                    title="Καθάρισμα"> <img src="{{ URL::to('/') }}/images/clear.ico"
                                                        height="20" /></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>

                            <hr>

                            @php($i = 1)
                            @foreach ($users as $u)
                                @if ($i % 2)
                                    <div class='row'>
                                    @else
                                        <div class='row bg-info'>
                                @endif
                                <div class='col-md-4 col-sm-4'>
                                    <div class='row'>
                                        <div class="col-md-1 col-sm-1 form-control-static">
                                            {{ ($users->currentPage() - 1) * $users->perPage() + $i }}
                                        </div>
                                        <div class="col-md-6 col-sm-6 form-control-static">
                                            {{ $u->name }}
                                        </div>
                                        <div class="col-md-4 col-sm-4 form-control-static">
                                            {{ $u->username }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-2 form-control-static">
                                    {{ $u->email }}
                                </div>
                                <div class="col-md-3 col-sm-3 form-control-static  text-center">
                                    ******
                                </div>

                                <div class="col-md-3 col-sm-3 ">
                                    <div class='row'>

                                        <div class="col-md-5 col-sm-5 form-control-static">
                                            {{ $u->role->role }}
                                        </div>
                                        <div class="col-md-2 col-sm-2 form-control-static  text-center">
                                            @if ($u->active)
                                                &#10004;
                                            @endif
                                        </div>
                                        <div class="col-md-5 col-sm-5 form-control-static  text-center">
                                            <a href="{{ URL::to('/') }}/users/{{ $u->id }}"
                                                class="{{ $submitVisible }}" title="Επεξεργασία {{ $u->name }}">
                                                <img src="{{ URL::to('/') }}/images/edit.ico" alt="edit" height="15">
                                            </a>
                                            <a href="#" id='deluser{{ $u->id }}' class="{{ $submitVisible }}"
                                                title="Διαγραφή {{ $u->name }}"
                                                onclick="chkdelete('{{ $u->id }}','{{ $u->name }}')"> <img
                                                    src="{{ URL::to('/') }}/images/delete.ico" alt="delete" height="15">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        @php($i++)
                        @endforeach

                        @if ($users->hasPages() and $users->count() > $users->perPage() / 2)
                            <div class='row'>
                                <div class="col-md-12 col-sm-12 small text-center">
                                    <span class="small">{{ $users->links() }}</span>
                                </div>
                            </div>
                        @endif


                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
