<?php

namespace App\Http\Controllers\Crm;

use App\Exports\Crm\MonthlyLeadTrendReportExport;
use App\Exports\Crm\SalesPersonPerformanceReportExport;
use App\Exports\Crm\SourceWiseLeadReportExport;
use App\Exports\Crm\StatusWiseLeadReportExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\LeadDashboardFilterRequest;
use App\Models\Crm\Lead;
use App\Services\Crm\LeadDashboardReportService;
use App\Services\Crm\LeadNotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DashboardController extends Controller
{
    public function __construct(
        private readonly LeadDashboardReportService $reportService,
        private readonly LeadNotificationService $notificationService
    )
    {
    }

    public function index(LeadDashboardFilterRequest $request): View
    {
        $this->authorize('viewAny', Lead::class);

        $filters = $request->validated();
        $dashboardData = $this->reportService->getDashboardData($request->user(), $filters);

        return view('crm.dashboard.index', [
            'filters' => $filters,
            'summaryCards' => $dashboardData['summaryCards'],
            'reports' => $dashboardData['reports'],
            'charts' => $dashboardData['charts'],
            'reminders' => $this->notificationService->dashboardReminderData($request->user()),
        ] + $this->reportService->filters());
    }

    public function exportExcel(LeadDashboardFilterRequest $request, string $report): BinaryFileResponse
    {
        $this->authorize('export', Lead::class);

        $filters = $request->validated();
        $dataset = $this->reportService->reportDataset($request->user(), $filters, $report);
        $definition = $this->reportService->reportDefinition($report);

        $export = match ($report) {
            'source-wise' => new SourceWiseLeadReportExport($dataset),
            'status-wise' => new StatusWiseLeadReportExport($dataset),
            'sales-performance' => new SalesPersonPerformanceReportExport($dataset),
            'monthly-trend' => new MonthlyLeadTrendReportExport($dataset),
            default => abort(404),
        };

        return Excel::download(
            $export,
            str($definition['title'])->slug('-') . '-' . now()->format('Ymd-His') . '.xlsx'
        );
    }

    public function exportPdf(LeadDashboardFilterRequest $request, string $report): Response
    {
        $this->authorize('export', Lead::class);

        $filters = $request->validated();
        $dataset = $this->reportService->reportDataset($request->user(), $filters, $report);
        $definition = $this->reportService->reportDefinition($report);

        $rows = $dataset->map(function ($row) use ($report): array {
            return match ($report) {
                'source-wise' => [
                    $row->label,
                    (int) $row->total,
                ],
                'status-wise' => [
                    $row->name,
                    (int) $row->total,
                    number_format((float) $row->expected_value, 2),
                ],
                'sales-performance' => [
                    $row->sales_person,
                    (int) $row->total_leads,
                    (int) $row->won_leads,
                    (int) $row->converted_leads,
                    number_format((float) $row->conversion_rate, 1) . '%',
                    number_format((float) $row->pipeline_value, 2),
                ],
                'monthly-trend' => [
                    $row->period_label,
                    (int) $row->total,
                ],
                default => [],
            };
        });

        $pdf = Pdf::loadView('crm.exports.report-pdf', [
            'title' => $definition['title'],
            'columns' => $definition['columns'],
            'rows' => $rows,
            'appliedFilters' => $this->reportService->filterSummary($filters),
        ])->setPaper('a4', 'portrait');

        return $pdf->download(str($definition['title'])->slug('-') . '-' . now()->format('Ymd-His') . '.pdf');
    }
}
