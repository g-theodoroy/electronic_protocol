
<div class="panel panel-default col-md-12 col-sm-12" id="showFindData">

    @if ($foundProtocolsCount > 0)

        <div class="row bg-info">
            <div class="form-control-static text-center">
                Η αναζήτηση επέστρεψε
                <strong>{{ $foundProtocolsCount }}</strong>
                {{ $foundProtocolsCount == 1 ? 'αποτέλεσμα' : 'αποτελέσματα' }}.
            </div>
        </div>

        @if ($protocols->hasPages())
            <div class="row">
                <div class="col-md-12 col-sm-12 small text-center">
                    {{ $protocols->links() }}
                </div>
            </div>
        @endif

        {{-- Highlight helper: escape original text, then add safe markup --}}
        @php
            $highlight = function ($text, $search) {
                $text = e((string) ($text ?? ''));
                $search = e(trim((string) ($search ?? '')));

                if ($search === '') {
                    return $text;
                }

                $pattern = '~(' . preg_quote($search, '~') . ')~iu';

                return preg_replace_callback(
                    $pattern,
                    function ($matches) {
                        return '<mark><strong>' . $matches[0] . '</strong></mark>';
                    },
                    $text
                ) ?? $text;
            };
        @endphp

        {{-- Column headings --}}
        <div class="row bg-primary">
            <div class="form-control-static col-md-1 col-sm-1 text-center">
                Αρ.Πρωτ.
            </div>

            <div class="form-control-static col-md-1 col-sm-1 text-center">
                Ημ.Πρωτ.
            </div>

            <div class="form-control-static col-md-4 col-sm-4">
                {{ $fields[$searchField1] ?? $searchField1 }}
            </div>

            <div class="form-control-static col-md-3 col-sm-3">
                {{ $fields[$searchField2] ?? $searchField2 }}
            </div>

            <div class="form-control-static col-md-3 col-sm-3">
                {{ $fields[$searchField3] ?? $searchField3 }}
            </div>
        </div>

        @php($i = 0)

        @foreach ($protocols as $protocol)

            <div class="row line {{ $i % 2 ? '' : 'line2' }}"
                 onclick="window.location.href='{{ url('/home/' . $protocol->id) }}'">

                {{-- Protocol number --}}
                <div class="col-md-1 col-sm-1 text-center">

                    <a href="{{ url('/home/' . $protocol->id . '/1') }}"
                       role="button"
                       title="Αντιγραφή ως Νέο"
                       onclick="event.stopPropagation();">

                        <img src="{{ url('/images/copy-stamp.png') }}"
                             height="20"
                             alt="Αντιγραφή">
                    </a>

                    {{ $protocol->protocolnum }}

                </div>

                {{-- Protocol date --}}
                <div class="col-md-1 col-sm-1 text-center">
                    {{ $protocol->protocoldate }}
                </div>

                {{-- Search field 1 --}}
                @if (array_key_exists($searchField1, $attachmentfields))

                    <div class="col-md-4 col-sm-4">
                        <ul class="list-inline">
                            @foreach ($protocol->attachments as $attachment)
                                <li>
                                    {!! $highlight(
                                        $attachment->$searchField1 ?? '',
                                        $searchData1
                                    ) !!}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                @else

                    <div class="col-md-4 col-sm-4">
                        {!! $highlight(
                            $protocol->$searchField1 ?? '',
                            $searchData1
                        ) !!}
                    </div>

                @endif

                {{-- Search field 2 --}}
                @if (array_key_exists($searchField2, $attachmentfields))

                    <div class="col-md-3 col-sm-3">
                        <ul class="list-inline">
                            @foreach ($protocol->attachments as $attachment)
                                <li>
                                    {!! $highlight(
                                        $attachment->$searchField2 ?? '',
                                        $searchData2
                                    ) !!}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                @else

                    <div class="col-md-3 col-sm-3">
                        {!! $highlight(
                            $protocol->$searchField2 ?? '',
                            $searchData2
                        ) !!}
                    </div>

                @endif

                {{-- Search field 3 --}}
                @if (array_key_exists($searchField3, $attachmentfields))

                    <div class="col-md-3 col-sm-3">
                        <ul class="list-inline">
                            @foreach ($protocol->attachments as $attachment)
                                <li>
                                    {!! $highlight(
                                        $attachment->$searchField3 ?? '',
                                        $searchData3
                                    ) !!}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                @else

                    <div class="col-md-3 col-sm-3">
                        {!! $highlight(
                            $protocol->$searchField3 ?? '',
                            $searchData3
                        ) !!}
                    </div>

                @endif

            </div>

            @php($i++)

        @endforeach

    @else

        <div class="row bg-info">
            <div class="form-control-static text-center">
                Δεν βρέθηκαν αποτελέσματα. Περιορίστε τα κριτήρια αναζήτησης.
            </div>
        </div>

    @endif

</div>

<script>
    // Delegated event: works with pagination links loaded through AJAX.
    $(document)
        .off('click.protocolSearchPagination', '#showFindData .pagination a')
        .on('click.protocolSearchPagination', '#showFindData .pagination a', function(e) {

            e.preventDefault();

            let page = new URL(this.href, window.location.href)
                .searchParams.get('page') || 1;

            getFindData(page);
        });
</script>

<style>
    #showFindData .line {
        padding-top: 7px;
        padding-bottom: 5px;
        cursor: pointer;
    }

    #showFindData .line:hover {
        background-color: #E6E6E6;
    }

    #showFindData .line2 {
        background-color: WhiteSmoke;
    }
</style>
