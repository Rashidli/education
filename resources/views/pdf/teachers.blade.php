@extends('pdf._layout')

@section('title', 'Müəllim qazancı hesabatı')

@section('content')
    <table>
        <thead>
            <tr>
                <th>Müəllim</th>
                <th>Növ</th>
                <th>Ödəniş</th>
                <th class="text-right">Gəlir</th>
                <th>Komissiya %</th>
                <th class="text-right">Qazanc</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td>{{ $row['teacher']->name }}</td>
                    <td>{{ $row['teacher']->typeLabel() }}</td>
                    <td>{{ $row['payments_count'] }}</td>
                    <td class="text-right">{{ number_format($row['revenue'], 2) }} AZN</td>
                    <td>{{ rtrim(rtrim(number_format($row['commission_rate'], 2), '0'), '.') }}%</td>
                    <td class="text-right">{{ number_format($row['earnings'], 2) }} AZN</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">Cəmi</td>
                <td class="text-right">{{ number_format($totals['revenue'], 2) }} AZN</td>
                <td></td>
                <td class="text-right">{{ number_format($totals['earnings'], 2) }} AZN</td>
            </tr>
        </tfoot>
    </table>
@endsection
