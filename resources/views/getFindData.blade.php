<div class="panel panel-default col-md-12 col-sm-12 " id='showFindData' >
    @if($foundProtocolsCount > 0)
    @if($maxRowsInFindPage < $foundProtocolsCount)
    <div class="row bg-warning">
        <div class="form-control-static text-center" ><strong>Η αναζήτηση επέστρεψε {{$foundProtocolsCount}} αποτελέσματα. Εμφανίζονται μόνο {{$maxRowsInFindPage}}. Εισάγετε περισσότερα κριτήρια αναζήτησης.</strong></div>
    </div>
    @else
    <div class="row bg-success">
        <div class="form-control-static  text-center" ><strong>Η αναζήτηση επέστρεψε {{$foundProtocolsCount}} αποτελέσματα.</strong></div>
    </div>
    @endif
    <div class="row bg-primary">
        <div class="form-control-static col-md-1 col-sm-1 text-center " >Αρ.Πρωτ.</div>
        <div class="form-control-static col-md-1 col-sm-1  text-center " >Ημ.Πρωτ.</div>
        <div class="form-control-static col-md-4 col-sm-4  " >{{$fields[$searchField1]}}</div>
        <div class="form-control-static col-md-3 col-sm-3  " >{{$fields[$searchField2]}}</div>
        <div class="form-control-static col-md-2 col-sm-2  " >{{$fields[$searchField3]}}</div>
    </div>
    @php($i=0)
    @foreach($protocols as $protocol)
    @if($i % 2)
    <div class="row bg-info">
    @else
    <div class="row">
    @endif
        <div class="col-md-1 col-sm-1 text-center " >{{$protocol->protocolnum}}</div>
        <div class="col-md-1 col-sm-1 text-center " >{{$protocol->protocoldate}}</div>
        @if (array_key_exists($searchField1,$attachmentfields))
            <div class="col-md-4 col-sm-4 " >
              <ul class='list-inline'>
              @foreach ($protocol->attachments()->get() as $attachment)
              <li>
              {!! str_ireplace($searchData1, "<mark><strong>$searchData1</strong></mark>", $attachment->$searchField1) !!}
            </li>
              @endforeach
            </ul>
            </div>
        @else
            <div class="col-md-4 col-sm-4 " >{!! str_ireplace($searchData1, "<mark><strong>$searchData1</strong></mark>", $protocol->$searchField1) !!}</div>
        @endif
        @if (array_key_exists($searchField2,$attachmentfields))
              <div class="col-md-3 col-sm-3 " >
                <ul class='list-inline'>
                @foreach ($protocol->attachments()->get() as $attachment)
                <li>
                  {!! str_ireplace($searchData2, "<mark><strong>$searchData2</strong></mark>", $attachment->$searchField2) !!}
                </li>
                @endforeach
                </div>
        @else
          <div class="col-md-3 col-sm-3 " >{!! str_ireplace($searchData2, "<mark><strong>$searchData2</strong></mark>", $protocol->$searchField2) !!}</div>
        @endif
        @if (array_key_exists($searchField3,$attachmentfields))
        <div class="col-md-2 col-sm-2 " >
          <ul class='list-inline'>
          @foreach ($protocol->attachments()->get() as $attachment)
          <li>
            {!! str_ireplace($searchData3, "<mark><strong>$searchData3</strong></mark>", $attachment->$searchField3) !!}
          </li>
          @endforeach
          </div>
        @else
          <div class="col-md-2 col-sm-2 " >{!! str_ireplace($searchData3, "<mark><strong>$searchData3</strong></mark>", $protocol->$searchField3) !!}</div>
        @endif
        <div class="col-md-1 col-sm-1 text-center" ><a href="{{ URL::to('/') }}/home/{{$protocol->id}}" class="" role="button" title="Μετάβαση" > <img src="{{ URL::to('/') }}/images/open.png" height="20" /></a></div>
    </div>
    @php($i++)
    @endforeach
    @else
    <div class="row bg-info">
        <div class="form-control-static  text-center"><strong>Δεν βρέθηκαν αποτελέσματα. Περιορίστε τα κριτήρια αναζήτησης.</strong></div>
    </div>
    @endif
</div>
