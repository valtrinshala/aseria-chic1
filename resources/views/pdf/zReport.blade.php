<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Receipt example</title>
    <style>
        * {
            font-size: 12px;
            font-family: 'Times New Roman';
        }

        table {
            width: 100%;
        }

        .centered {
            text-align: center;
            align-content: center;
            margin-top: 0;
        }

        .centered.img {
            justify-content: center;
            display: flex;
        }

        .ticket {
            width: 300px;
            max-width: 400px;
        }

        img {
            max-width: 35px;
        }
    </style>
</head>
<body>
<div class="ticket">
    <div style="position: relative; height: 30px;">
        <img style="position: absolute; left: 50%; height: 30px; transform: translateX(-50%);" class="centered img" src="{{ $settings->getClientImage() }}" alt="Logo">
    </div>
    <p class="centered">
        <br>{{ $data['header']['row_data'][0] }}
        <br>{{ $data['header']['row_data'][1] }}
        <br>{{ $data['header']['row_data'][2] }}
        <br>{{ $data['header']['row_data'][4] }}
        <br>{{ $data['header']['row_data'][6] }}
        <br>{{ $data['header']['row_data'][8] }}
    </p>
    <table>
        <tbody>
        @foreach($data['totals']['row_data'] as  $key => $totals)
            @if(is_numeric($key) && ($key == 2 || $key == 4 || $key == 8))
                <tr>
                    <td style="width: 50%">&nbsp;</td>
                    <td style="width: 50%"></td>
                </tr>
            @elseif(!is_numeric($key))
                <tr>
                    <td style="width: 50%;">{{ $key }}</td>
                    <td style="width: 50%; text-align: right">{{ $totals }}</td>
                </tr>
            @endif
        @endforeach
        <tr>
            <td style="width: 50%">&nbsp;</td>
            <td style="width: 50%"></td>
        </tr>
        @foreach($data['sales_by_service']['row_data'] as $key1 => $salesByService)
            @if(!is_numeric($key1))
                <tr>
                    <td style="width: 50%; vertical-align: bottom; margin: 0; @if($key1 == 'row_data_header') border-bottom: 1px solid black; height: 30px; @elseif($key1 == 'row_data_total') /*border-top: 1px solid black;*/ @else height: 17px; @endif">{{ $salesByService[0] }}</td>
                    <td style="width: 50%;  margin: 10px; @if($key1 == 'row_data_header') border-bottom: 1px solid black; @elseif($key1 == 'row_data_total'){{-- border-top: 1px solid black;--}} @else height: 17px; @endif">
                        <div style="margin: 0; vertical-align: bottom;">
                            <div style="float: left; width: 50%; margin: 0; padding: 0">
                                {{ $salesByService[1] }}
                            </div>
                            <div style="float: right; vertical-align: bottom; width: 50%; text-align: right; margin-top: 0px;  padding: 0">
                                {{ $salesByService[2] }}
                            </div>
                        </div>
                    </td>
                </tr>
            @endif
        @endforeach
        <tr>
            <td style="width: 50%">&nbsp;</td>
            <td style="width: 50%"></td>
        </tr>
        <tr>
            <td style="width: 50%">{{ $data['sales_by_categories']['title'] }}</td>
            <td style="width: 50%"></td>
        </tr>
        <tr>
            <td style="width: 50%; vertical-align: bottom; margin: 0; border-bottom: 1px solid black; height: 30px; ">{{ $data['sales_by_categories']['row_head'][0] }}</td>
            <td style="width: 50%;  margin: 0; border-bottom: 1px solid black; height: 30px;">
                <div style="margin: 0; vertical-align: bottom;">
                    <div style="float: left; width: 50%; margin: 0; padding: 0">
                        {{ $data['sales_by_categories']['row_head'][1] }}
                    </div>
                    <div style="float: right; vertical-align: bottom; width: 50%; text-align: right; margin: 0;  padding: 0">
                        {{ $data['sales_by_categories']['row_head'][2] }}
                    </div>
                </div>
            </td>
        </tr>
        @foreach($data['sales_by_categories']['row_data'] as $categories)
            <tr>
                <td style="width: 50%;">{{ $categories['name'] }}</td>
                <td style="width: 50%;">
                    <div>
                        <div style="float: left; width: 50%;">
                            {{ $categories['sales'] }}
                        </div>
                        <div style="float: right; width: 50%; text-align: right;">
                            {{ $categories['net_amount'] }}
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
        <tr>
            <td style="width: 50%;">{{ $data['sales_by_categories']['row_footer'][0] }}</td>
            <td style="width: 50%;">
                <div>
                    <div style="float: left; width: 50%;">
                        {{ $data['sales_by_categories']['row_footer'][1] }}
                    </div>
                    <div style="float: right; width: 50%; text-align: right;">
                        {{ $data['sales_by_categories']['row_footer'][2] }}
                    </div>
                </div>
            </td>
        </tr>

        <tr>
            <td style="width: 50%">&nbsp;</td>
            <td style="width: 50%"></td>
        </tr>
        @foreach($data['final']['row_data'] as  $key => $finals)
            @if(is_numeric($key) && ($key == 2 || $key == 4 || $key == 8))
                <tr>
                    <td>&nbsp;</td>
                    <td></td>
                </tr>
            @elseif(!is_numeric($key))
                <tr>
                    <td>{{ $key }}</td>
                    <td style="text-align: right">{{ $finals }}</td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
