        <table>
            <thead>
                <tr>
                    <th colspan=2>{{ $ipiresiasName }}</th>
                    <th colspan=6>ΠΡΩΤΟΚΟΛΛΟ ΕΤΟΥΣ {{ $etos }}</th>
                </tr>
                <tr>
                    <th rowspan=2>Αύξ.Αριθ.<br>Ημ.Παραλ.</th>
                    <th colspan=3>ΕΙΣΕΡΧΟΜΕΝΑ</th>
                    <th colspan=2>ΕΞΕΡΧΟΜΕΝΑ</th>
                    <th>ΔΙΕΚΠΕΡΑΙΩΣΗ</th>
                    <th rowspan=2>Φάκελος<br>&#x2727;Σχετ.αριθμοί<br>&#x2726;Παρατηρήσεις</th>
                </tr>
                <tr>
                    <th>
                        Αριθ/Ημ.Εισερχ.<br>&#x2727;Τόπος έκδοσης<br>&#x2726;Αρχή Έκδοσης</th>
                    <th>Θέμα<br>&#x2727;Περίληψη Εισερχομένου</th>
                    <th>Παραλήπτης</th>
                    <th>Ημνια Εξερχ.<br>&#x2727;Απευθύνεται</th>
                    <th>Περίληψη Εξερχόμενου</th>
                    <th>Διεκπεραίωση<br>&#x2727;Ημνια Διεκπ.</th>
                </tr>
                <tr>
                    <th>1, 2</th>
                    <th>3, 4, 5</th>
                    <th>6</th>
                    <th>7</th>
                    <th>10, 8</th>
                    <th>9</th>
                    <th>11</th>
                    <th>13, 12</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($protocols as $protocol)
                    <tr>
                        <td>
                            <strong>{{ $protocol->protocolnum }}</strong><br>
                            {{ $protocol->protocoldate }}
                        </td>
                        <td>

                            @if ($protocol->in_num)
                                {{ $protocol->in_num }}/
                            @endif
                            {{ $protocol->in_date }}
                            @if ($protocol->in_date and $protocol->in_topos_ekdosis)
                                <br>
                            @endif
                            @if ($protocol->in_topos_ekdosis)
                                &#x2727;{{ $protocol->in_topos_ekdosis }}
                            @endif
                            @if ($protocol->in_topos_ekdosis and $protocol->in_arxi_ekdosis)
                                <br>
                            @endif
                            @if (!$protocol->in_topos_ekdosis and ($protocol->in_date and $protocol->in_arxi_ekdosis))
                                <br>
                            @endif
                            @if ($protocol->in_arxi_ekdosis)
                                &#x2726;{{ $protocol->in_arxi_ekdosis }}
                            @endif
                        </td>
                        <td>{{ $protocol->thema }}
                            @if ($protocol->in_perilipsi)
                                <br>&#x2727;{{ $protocol->in_perilipsi }}
                            @endif
                        </td>
                        <td>{{ $protocol->in_paraliptis }}</td>
                        <td>
                            {{ $protocol->out_date }}
                            @if ($protocol->out_date and $protocol->out_to)
                                <br>
                            @endif
                            @if ($protocol->out_to)
                                &#x2727;{{ $protocol->out_to }}
                            @endif
                        </td>
                        <td>{{ $protocol->out_perilipsi }}</td>
                        <td>
                            @if ($protocol->diekperaiosi)
                                @php($str = 'd')
                                @foreach (explode(',', $protocol->diekperaiosi) as $d)
                                    @if ($d && strpos($str, substr($d, 0, 1)) !== false)
                                        @if ($myUsers->where('id', '==', ltrim($d, $str))->count())
                                            {{ $myUsers->where('id', '==', ltrim($d, $str))->first()->name }}
                                        @else
                                            {{ ltrim($d, $str) }}
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                            @if ($protocol->diekp_date)
                                <br>&#x2727;{{ $protocol->diekp_date }}
                            @endif
                        </td>
                        <td>
                            {{ $protocol->fakelos }}
                            @if ($protocol->fakelos and $protocol->sxetiko)
                                <br>
                            @endif
                            @if ($protocol->sxetiko)
                                &#x2727;{{ $protocol->sxetiko }}
                            @endif
                            @if ($protocol->sxetiko and $protocol->paratiriseis)
                                <br>
                            @endif
                            @if (!$protocol->sxetiko and ($protocol->fakelos and $protocol->paratiriseis))
                                <br>
                            @endif
                            @if ($protocol->paratiriseis)
                                &#x2726;{{ $protocol->paratiriseis }}
                            @endif

                        </td>
                    </tr>
                @endforeach
                @if (!count($protocols))
                    <tr>
                        <td colspan=8>Δεν υπάρχουν Πρωτόκολλα τα οποία ικανοποιούν τα κριτήρια που θέσατε.</td>
                    </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td colspan=8>Εξήχθη: {{ $datetime }}</td>
                </tr>
            </tfoot>
        </table>
