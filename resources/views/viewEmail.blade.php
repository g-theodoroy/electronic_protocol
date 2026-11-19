
<!DOCTYPE html>
<html lang="el">
 
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
 
    <title>Εκτύπωση email </title>
 
    @php
        use ZBateson\MailMimeParser\Header\HeaderConsts;
 
        $fmtAddr = function ($addr) {
            if (!$addr) {
                return '';
            }
            return $addr->getName() ? $addr->getName() . ' <' . $addr->getEmail() . '>' : $addr->getEmail();
        };
        $fmtAddrList = function ($header) use ($fmtAddr) {
            if (!$header || !$header->getRawValue()) {
                return '';
            }
            return implode(', ', array_map($fmtAddr, $header->getAddresses()));
        };
 
        // Ίδιο timezone με την άλλη όψη: από τον controller ή από τις ρυθμίσεις
        $tz = $timeZone ?? \App\Config::getConfigValueOf('timeZone');
 
        $subject = (string) $mailMessage->getHeaderValue(HeaderConsts::SUBJECT);
 
        $fromHeader = $mailMessage->getHeader(HeaderConsts::FROM);
        $fromAddr = $fromHeader ? $fromHeader->getAddresses()[0] ?? null : null;
        $fromDisplay = $fmtAddr($fromAddr);
 
        $dateHeader = $mailMessage->getHeader(HeaderConsts::DATE);
        $dateObj = $dateHeader ? $dateHeader->getDateTime() : null;
        $dateFull = $dateObj ? \Carbon\Carbon::parse($dateObj)->timezone($tz)->format('d/m/Y H:i:s') : '';
 
        $toList = $fmtAddrList($mailMessage->getHeader(HeaderConsts::TO));
        $ccList = $fmtAddrList($mailMessage->getHeader(HeaderConsts::CC));
        $replyList = $fmtAddrList($mailMessage->getHeader(HeaderConsts::REPLY_TO));
 
        $htmlContent = (string) $mailMessage->getHtmlContent();
        $textContent = (string) $mailMessage->getTextContent();
        $attachmentNames = [];
        if ($mailMessage->getAttachmentCount()) {
            foreach ($mailMessage->getAllAttachmentParts() as $attachment) {
                $attachmentNames[] = (string) $attachment->getFilename();
            }
        }
    @endphp
 
    <!-- Styles -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
        integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
 
<body>
    <div class="container">
        <div class="row">&nbsp;</div>
        <div class="panel panel-default col-md-12 col-sm-12  ">
            <div class="row bg-warning">
                <div class="form-control-static col-md-1 col-sm-1  "><strong>Από:</strong></div>
                <div class="form-control-static col-md-8 col-sm-8  ">{{ $fromDisplay }}</div>
                <div class="form-control-static col-md-1 col-sm-1 "><strong>Ημνία:</strong></div>
                <div class="form-control-static col-md-2 col-sm-2 ">{{ $dateFull }}</div>
            </div>
            <div class="row bg-warning ">
                <div class="form-control-static col-md-1 col-sm-1"><strong>Θέμα:</strong></div>
                <div class="form-control-static col-md-11 col-sm-11  "><strong>{{ $subject }}</strong></div>
            </div>
            @if ($toList !== '')
                <div class="row bg-warning ">
                    <div class="form-control-static col-md-1 col-sm-1"><strong>Προς:</strong></div>
                    <div class="form-control-static col-md-11 col-sm-11">{{ $toList }}</div>
                </div>
            @endif
            @if ($ccList !== '')
                <div class="row bg-warning ">
                    <div class="form-control-static col-md-1 col-sm-1"><strong>Κοιν:</strong></div>
                    <div class="form-control-static col-md-11 col-sm-11">{{ $ccList }}</div>
                </div>
            @endif
            @if ($replyList !== '')
                <div class="row bg-warning ">
                    <div class="form-control-static col-md-1 col-sm-1"><strong>Απάντηση:</strong></div>
                    <div class="form-control-static col-md-11 col-sm-11">{{ $replyList }}</div>
                </div>
            @endif
            <hr>
            @if (strlen($htmlContent))
                <div class="row">
                    <div class="form-control-static col-md-12 col-sm-12  ">
                        {{-- Το HTML του email δεν εκτυπώνεται πια ανεπεξέργαστο στη σελίδα (XSS).
                             Εμφανίζεται σε iframe με sandbox ΧΩΡΙΣ allow-scripts, άρα δεν τρέχει JavaScript. --}}
                        <iframe id="emailHtmlFrame" sandbox="allow-same-origin" width="100%" frameBorder="0"
                            srcdoc="{{ str_replace('iso-8859-7', 'utf-8', $htmlContent) }}"
                            onload="this.style.height=(this.contentWindow.document.documentElement.scrollHeight+10)+'px';"></iframe>
                    </div>
                </div>
            @endif
            @if (strlen($htmlContent) && strlen($textContent))
                <hr>
            @endif
            @if (strlen($textContent))
                <div class="row">
                    <div class="form-control-static col-md-12 col-sm-12  small"
                        style="white-space: pre-wrap; overflow: hidden">{{ $textContent }}</div>
                </div>
            @endif
            <hr>
            @if (count($attachmentNames))
                <div class="row bg-warning">
                    <div class="form-control-static col-md-2 col-sm-2"><strong>Συνημμένα:</strong></div>
                    <div class="form-control-static col-md-10 col-sm-10 ">{{ implode(', ', $attachmentNames) }}</div>
                </div>
            @endif
        </div>
    </div>
</body>
 
</html>
 