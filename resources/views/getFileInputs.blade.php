    @php($k=1)
    <div class="row ">
        <div class="col-md-1 col-sm-1 small text-center form-control-static">
            <strong>Αρχείο</strong> 
        </div>
        <div class="col-md-4 col-sm-4 small form-control-static fileinput fileinput-new" data-provides="fileinput">
            <span class="btn btn-default btn-file"><span class="fileinput-new">
                <img src="{{ URL::to('/') }}/images/find.ico" height=20 />
            </span>
            <span class="fileinput-exists"><img src="{{ URL::to('/') }}/images/edit.ico" height=20 /></span>
            <input id="att{{$k}}" type="file" name="att{{$k}}"></span>
            <span class="fileinput-filename"></span>
            <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none" title="Διαγραφή"><img src="{{ URL::to('/') }}/images/delete.ico" height=20 /></a>
        </div>
        <div class="col-md-4 col-sm-4 small form-control-static fileinput fileinput-new" data-provides="fileinput">
            <span class="btn btn-default btn-file"><span class="fileinput-new">
                <img src="{{ URL::to('/') }}/images/find.ico" height=20 />
            </span>
            <span class="fileinput-exists"><img src="{{ URL::to('/') }}/images/edit.ico" height=20 /></span>
            <input id="att{{$k+1}}" type="file" name="att{{$k+1}}"></span>
            <span class="fileinput-filename"></span>
            <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none" title="Διαγραφή"><img src="{{ URL::to('/') }}/images/delete.ico" height=20 /></a>
        </div>
        <div class="col-md-1 col-sm-1 small text-center form-control-static">
            <strong>Διατήρηση</strong> 
        </div>
        <div class="col-md-2 col-sm-2 small form-control-static fileinput fileinput-new  text-center" data-provides="fileinput">
        <select id="keep" class="form-control" name="keep" >
            @if($allowUserChangeKeepSelect)
                <option value=''></option>
                @foreach($years as $year)
                    @if($year->keep == $keepval) 
                        <option value='{{$year->keep}}' selected>{{$year->keep}} {{ $year->keep > 1 ? 'χρόνια' : 'χρόνο' }} </option>
                    @else
                        <option value='{{$year->keep}}' >{{$year->keep}} {{ $year->keep > 1 ? 'χρόνια' : 'χρόνο' }} </option>
                    @endif
                    @endforeach
                @foreach($words as $word)
                    @if($word->keep_alt == $keepval) 
                        <option value='{{$word->keep_alt}}' selected>{{$word->keep_alt}}</option>
                    @else
                        <option value='{{$word->keep_alt}}'>{{$word->keep_alt}}</option>
                    @endif
                @endforeach
            @else
                @foreach($years as $year)
                    @if($year->keep == $keepval) 
                        <option value='{{$year->keep}}' selected>{{$year->keep}} {{ $year->keep > 1 ? 'χρόνια' : 'χρόνο' }} </option>
                    @endif
                @endforeach
                @foreach($words as $word)
                    @if($word->keep_alt == $keepval) 
                       <option value='{{$word->keep_alt}}' selected>{{$word->keep_alt}}</option>
                    @endif
                @endforeach
            @endif
        </select>
        </div>
    </div>

    @for ($i = 0; $i < $num; $i++)
    @php($k+=2)
    <div class="row ">
        <div class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 small form-control-static fileinput fileinput-new" data-provides="fileinput">
            <span class="btn btn-default btn-file"><span class="fileinput-new">
                <img src="{{ URL::to('/') }}/images/find.ico" height=20 />
            </span>
            <span class="fileinput-exists"><img src="{{ URL::to('/') }}/images/edit.ico" height=20 /></span>
            <input id="att{{$k}}" type="file" name="att{{$k}}"></span>
            <span class="fileinput-filename"></span>
            <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none" title="Διαγραφή"><img src="{{ URL::to('/') }}/images/delete.ico" height=20 /></a>
        </div>
        <div class="col-md-4 col-sm-4 small form-control-static fileinput fileinput-new" data-provides="fileinput">
            <span class="btn btn-default btn-file"><span class="fileinput-new">
                <img src="{{ URL::to('/') }}/images/find.ico" height=20 />
            </span>
            <span class="fileinput-exists"><img src="{{ URL::to('/') }}/images/edit.ico" height=20 /></span>
            <input id="att{{$k+1}}" type="file" name="att{{$k+1}}"></span>
            <span class="fileinput-filename"></span>
            <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none" title="Διαγραφή"><img src="{{ URL::to('/') }}/images/delete.ico" height=20 /></a>
        </div>
    </div>
    @endfor
