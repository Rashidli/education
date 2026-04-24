<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(private ReportService $reports) {}

    public function index()
    {
        return view('reports.index');
    }

    public function monthly(Request $request)
    {
        [$from, $to] = $this->dateRange($request, subMonths: 6);
        $rows = $this->reports->monthlyFinancial($from, $to);

        return $this->respondWith($request, 'reports.monthly', [
            'rows' => $rows,
            'from' => $from,
            'to' => $to,
            'totals' => [
                'revenue' => (float) $rows->sum('revenue'),
                'commission' => (float) $rows->sum('teacher_commission'),
                'net' => (float) $rows->sum('net'),
                'payments' => (int) $rows->sum('payments_count'),
            ],
        ], fn ($rows) => $this->csvFromRows('aylıq-maliyyə', [
            'Ay', 'Ödəniş sayı', 'Gəlir', 'Müəllim komissiyası', 'Xalis',
        ], $rows->map(fn ($r) => [
            $r['month']->format('Y-m'),
            $r['payments_count'],
            number_format($r['revenue'], 2, '.', ''),
            number_format($r['teacher_commission'], 2, '.', ''),
            number_format($r['net'], 2, '.', ''),
        ])), 'pdf.monthly');
    }

    public function teachers(Request $request)
    {
        [$from, $to] = $this->dateRange($request, subMonths: 1);
        $rows = $this->reports->teacherEarnings($from, $to);

        return $this->respondWith($request, 'reports.teachers', [
            'rows' => $rows,
            'from' => $from,
            'to' => $to,
            'totals' => [
                'revenue' => (float) $rows->sum('revenue'),
                'earnings' => (float) $rows->sum('earnings'),
            ],
        ], fn ($rows) => $this->csvFromRows('müəllim-qazancı', [
            'Müəllim', 'Növ', 'Ödəniş sayı', 'Gəlir', 'Komissiya %', 'Qazanc',
        ], $rows->map(fn ($r) => [
            $r['teacher']->name,
            $r['teacher']->typeLabel(),
            $r['payments_count'],
            number_format($r['revenue'], 2, '.', ''),
            rtrim(rtrim(number_format($r['commission_rate'], 2), '0'), '.'),
            number_format($r['earnings'], 2, '.', ''),
        ])), 'pdf.teachers');
    }

    public function students(Request $request)
    {
        [$from, $to] = $this->dateRange($request, subMonths: 1);
        $groupId = $request->integer('group_id') ?: null;

        $rows = $this->reports->studentPayments($from, $to, $groupId);

        return $this->respondWith($request, 'reports.students', [
            'rows' => $rows,
            'from' => $from,
            'to' => $to,
            'groupId' => $groupId,
            'groups' => Group::where('is_active', true)->latest()->get(),
            'totals' => ['paid' => (float) $rows->sum('total_paid')],
        ], fn ($rows) => $this->csvFromRows('tələbə-ödənişləri', [
            'Tələbə', 'Telefon', 'Qrup sayı', 'Ödənilib',
        ], $rows->map(fn ($r) => [
            $r['student']->full_name,
            $r['student']->phone ?? '',
            $r['enrollments']->count(),
            number_format($r['total_paid'], 2, '.', ''),
        ])), 'pdf.students');
    }

    private function dateRange(Request $request, int $subMonths = 1): array
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : Carbon::now()->subMonths($subMonths)->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : Carbon::now()->endOfMonth();

        return [$from, $to];
    }

    private function respondWith(Request $request, string $view, array $data, callable $toCsv, string $pdfView)
    {
        abort_unless($request->user()->can('reports.view'), 403);

        $export = $request->string('export')->value();

        if ($export === 'csv') {
            abort_unless($request->user()->can('reports.export'), 403);

            return $toCsv($data['rows']);
        }

        if ($export === 'pdf') {
            abort_unless($request->user()->can('reports.export'), 403);

            return Pdf::loadView($pdfView, $data)
                ->setPaper('a4', 'portrait')
                ->download(str_replace('.', '-', $pdfView) . '-' . now()->format('Y-m-d') . '.pdf');
        }

        return view($view, $data);
    }

    private function csvFromRows(string $filename, array $headers, $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM for Excel
            fputcsv($out, $headers);
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename . '-' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
