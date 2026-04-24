@extends('pdf._layout')

@section('title', 'Tələbə ödənişləri hesabatı')

@section('content')
    <table>
        <thead>
            <tr>
                <th>Tələbə</th>
                <th>Telefon</th>
                <th>Qruplar</th>
                <th class="text-right">Ödənilib</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td>{{ $row['student']->full_name }}</td>
                    <td>{{ $row['student']->phone ?? '—' }}</td>
                    <td>{{ $row['enrollments']->pluck('group.name')->join(', ') }}</td>
                    <td class="text-right">{{ number_format($row['total_paid'], 2) }} AZN</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">Cəmi ({{ $rows->count() }} tələbə)</td>
                <td class="text-right">{{ number_format($totals['paid'], 2) }} AZN</td>
            </tr>
        </tfoot>
    </table>
@endsection
