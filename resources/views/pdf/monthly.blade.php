@extends('pdf._layout')

@section('title', 'Aylıq maliyyə hesabatı')

@section('content')
    <table>
        <thead>
            <tr>
                <th>Ay</th>
                <th>Ödəniş sayı</th>
                <th class="text-right">Gəlir</th>
                <th class="text-right">Komissiya</th>
                <th class="text-right">Xalis</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td>{{ $row['month']->translatedFormat('F Y') }}</td>
                    <td>{{ $row['payments_count'] }}</td>
                    <td class="text-right">{{ number_format($row['revenue'], 2) }} AZN</td>
                    <td class="text-right">{{ number_format($row['teacher_commission'], 2) }} AZN</td>
                    <td class="text-right">{{ number_format($row['net'], 2) }} AZN</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>Cəmi</td>
                <td>{{ $totals['payments'] }}</td>
                <td class="text-right">{{ number_format($totals['revenue'], 2) }} AZN</td>
                <td class="text-right">{{ number_format($totals['commission'], 2) }} AZN</td>
                <td class="text-right">{{ number_format($totals['net'], 2) }} AZN</td>
            </tr>
        </tfoot>
    </table>
@endsection
