    @php($k = 1)
    @for ($i = 0; $i <= $num; $i++)
        <div class="row ">
            <hr>
            <div class="col-md-4 col-sm-4 small form-control-static fileinput fileinput-new" data-provides="fileinput">
                <span class="btn btn-default btn-file"><span class="fileinput-new">
                        <img src="{{ URL::to('/') }}/images/find.ico" height=20 />
                    </span>
                    <span class="fileinput-exists"><img src="{{ URL::to('/') }}/images/edit.ico" height=20 /></span>
                    <input id="att{{ $k }}" type="file" name="att{{ $k }}"></span>
                <span class="fileinput-filename"></span>
                <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none"
                    title="Διαγραφή"><img src="{{ URL::to('/') }}/images/delete.ico" height=20 /></a>
            </div>
            <div class="col-md-4 col-sm-4 small form-control-static fileinput fileinput-new" data-provides="fileinput">
                <span class="btn btn-default btn-file"><span class="fileinput-new">
                        <img src="{{ URL::to('/') }}/images/find.ico" height=20 />
                    </span>
                    <span class="fileinput-exists"><img src="{{ URL::to('/') }}/images/edit.ico" height=20 /></span>
                    <input id="att{{ $k + 1 }}" type="file" name="att{{ $k + 1 }}"></span>
                <span class="fileinput-filename"></span>
                <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none"
                    title="Διαγραφή"><img src="{{ URL::to('/') }}/images/delete.ico" height=20 /></a>
            </div>
            <div class="col-md-4 col-sm-4 small form-control-static fileinput fileinput-new" data-provides="fileinput">
                <span class="btn btn-default btn-file"><span class="fileinput-new">
                        <img src="{{ URL::to('/') }}/images/find.ico" height=20 />
                    </span>
                    <span class="fileinput-exists"><img src="{{ URL::to('/') }}/images/edit.ico" height=20 /></span>
                    <input id="att{{ $k + 1 }}" type="file" name="att{{ $k + 2 }}"></span>
                <span class="fileinput-filename"></span>
                <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none"
                    title="Διαγραφή"><img src="{{ URL::to('/') }}/images/delete.ico" height=20 /></a>
            </div>
        </div>
        <div class="row ">
            <div class="col-md-2 col-sm-2 small form-control-static ">
                <input id="ada{{ $k }}" type="text" name="ada{{ $k }}"
                    class="form-control input text-center" placeholder="ΑΔΑ"
                    title="Επιλέξτε αρχείο ή πληκτρολογείστε ΑΔΑ">
            </div>
            <div class="col-md-2 col-sm-2">&nbsp;</div>
            <div class="col-md-2 col-sm-2 small form-control-static ">
                <input id="ada{{ $k + 1 }}" type="text" name="ada{{ $k + 1 }}"
                    class="form-control input text-center" placeholder="ΑΔΑ"
                    title="Επιλέξτε αρχείο ή πληκτρολογείστε ΑΔΑ">
            </div>
            <div class="col-md-2 col-sm-2">&nbsp;</div>
            <div class="col-md-2 col-sm-2 small form-control-static ">
                <input id="ada{{ $k + 2 }}" type="text" name="ada{{ $k + 2 }}"
                    class="form-control input text-center" placeholder="ΑΔΑ"
                    title="Επιλέξτε αρχείο ή πληκτρολογείστε ΑΔΑ">
            </div>
            <div class="col-md-2 col-sm-2">&nbsp;</div>
        </div>
        @php($k += 3)
    @endfor
