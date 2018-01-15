    @php($k=1)
    @for ($i = 0; $i <= $num; $i++)
    <div class="row ">
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
        <div class="col-md-4 col-sm-4 small form-control-static fileinput fileinput-new" data-provides="fileinput">
            <span class="btn btn-default btn-file"><span class="fileinput-new">
                <img src="{{ URL::to('/') }}/images/find.ico" height=20 />
            </span>
            <span class="fileinput-exists"><img src="{{ URL::to('/') }}/images/edit.ico" height=20 /></span>
            <input id="att{{$k+1}}" type="file" name="att{{$k+2}}"></span>
            <span class="fileinput-filename"></span>
            <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none" title="Διαγραφή"><img src="{{ URL::to('/') }}/images/delete.ico" height=20 /></a>
        </div>
    </div>
    @php($k+=3)
    @endfor
