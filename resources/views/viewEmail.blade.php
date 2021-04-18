<!DOCTYPE html>
<html lang="el">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

       <title>Εκτύπωση email </title>

        <!-- Styles -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    </head>
    <body>
      <div class="container">
        <div class="row">&nbsp;</div>
        <div class="panel panel-default col-md-12 col-sm-12  ">
          <div class="row bg-warning">
            <div class="form-control-static col-md-1 col-sm-1  "><strong>Από:</strong></div>
            <div class="form-control-static col-md-8 col-sm-8  ">@if($oMessage->getFrom()[0]->personal) {{mb_detect_encoding($oMessage->getFrom()[0]->personal, 'UTF-8, ISO-8859-7', true)== 'ISO-8859-7' ? iconv("ISO-8859-7", "UTF-8//IGNORE", $oMessage->getFrom()[0]->personal) . " <" : $oMessage->getFrom()[0]->personal . " <" }}@endif{{$oMessage->getFrom()[0]->mail}}@if($oMessage->getFrom()[0]->personal)&gt;@endif</div>
            <div class="form-control-static col-md-1 col-sm-1 "><strong>Ημνία:</strong></div>
            <div class="form-control-static col-md-2 col-sm-2 ">{{$oMessage->getDate()}}</div>
          </div>
          <div class="row bg-warning ">
            <div class="form-control-static col-md-1 col-sm-1"><strong>Θέμα:</strong></div>
            @php
            $subject = imap_utf8($oMessage->getSubject());
              if(! $subject) $subject = $oMessage->getSubject();
            @endphp
            <div class="form-control-static col-md-11 col-sm-11  "><strong>{{ $subject }}</strong></div>
          </div>
          @if($oMessage->getTo())
          <div class="row bg-warning ">
            <div class="form-control-static col-md-1 col-sm-1"><strong>Προς:</strong></div>
            <div class="form-control-static col-md-11 col-sm-11">
              @foreach($oMessage->getTo() as $getTo)
              {{$getTo->name}}{{$getTo->mail}}@if(! $loop->last),&nbsp;@endif
              @endforeach
            </div>
          </div>
          @endif
          @if($oMessage->getCc())
          <div class="row bg-warning ">
              <div class="form-control-static col-md-1 col-sm-1"><strong>Κοιν:</strong></div>
              <div class="form-control-static col-md-11 col-sm-11">
                @foreach($oMessage->getCc() as $getCc)
                {{$getCc->name}}{{$getCc->mail}}@if(! $loop->last),&nbsp;@endif
                @endforeach
              </div>
          </div>
          @endif
          @if($oMessage->getReplyTo())
          <div class="row bg-warning ">
              <div class="form-control-static col-md-1 col-sm-1"><strong>Απάντηση:</strong></div>
              <div class="form-control-static col-md-11 col-sm-11">
                @foreach($oMessage->getReplyTo() as $getReplyTo)
                {{$getReplyTo->name}}{{$getReplyTo->mail}}@if(! $loop->last),&nbsp;@endif
                @endforeach
              </div>
          </div>
          @endif
          <hr>
          @if($oMessage->hasHTMLBody())
          <div class="row">
            <div class="form-control-static col-md-12 col-sm-12  ">{!! str_replace("iso-8859-7", "utf-8", $oMessage->getHTMLBody()) !!}</div>
          </div>
          @endif
          @if($oMessage->hasHTMLBody() && $oMessage->hasTextBody())
          <hr>
          @endif
          @if($oMessage->hasTextBody())
          <div class="row">
            <div class="form-control-static col-md-12 col-sm-12  small">{{$oMessage->getTextBody()}}</div>
          </div>
          @endif
          <hr>
          @if($oMessage->hasAttachments())
          <div class="row bg-warning">
            <div class="form-control-static col-md-2 col-sm-2"><strong>Συνημμένα:</strong></div>
            <div class="form-control-static col-md-10 col-sm-10 ">
              @foreach($oMessage->attachments as $key=>$attachment)
                @php
                  $filename = imap_utf8($attachment->getName());
                  if (! $filename) $filename = $attachment->getName();
                @endphp
              {{ $filename }}
              @if(! $loop->last), &nbsp; @endif
              @endforeach
            </div>
          </div>
          @endif
        </div>
      </div>
    </body>
</html>
