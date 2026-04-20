<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\StoreLeadAttachmentRequest;
use App\Models\Crm\Lead;
use App\Models\Crm\LeadAttachment;
use App\Services\Crm\LeadActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LeadAttachmentController extends Controller
{
    public function __construct(private readonly LeadActivityLogger $activityLogger)
    {
    }

    public function store(StoreLeadAttachmentRequest $request, Lead $lead): RedirectResponse
    {
        $this->authorize('uploadAttachment', $lead);

        DB::transaction(function () use ($request, $lead): void {
            foreach ($request->file('attachments', []) as $file) {
                $extension = strtolower((string) $file->getClientOriginalExtension());
                $safeName = Str::uuid()->toString() . ($extension ? '.' . $extension : '');
                $directory = 'leads/' . $lead->id . '/attachments';
                $storedPath = $file->storeAs($directory, $safeName, 'public');

                $attachment = $lead->attachments()->create([
                    'file_name' => $safeName,
                    'file_original_name' => $file->getClientOriginalName(),
                    'file_path' => $storedPath,
                    'file_type' => $file->getMimeType() ?: 'application/octet-stream',
                    'file_extension' => $extension,
                    'file_size' => $file->getSize(),
                    'uploaded_by' => auth()->id(),
                ]);

                $lead->updated_by = auth()->id();
                $lead->save();

                $this->activityLogger->logAttachmentUploaded($lead, $attachment->file_original_name, auth()->user());
            }
        });

        return redirect()
            ->route('crm.leads.show', $lead)
            ->with('crm_success', 'Attachment uploaded successfully.');
    }

    public function preview(Lead $lead, LeadAttachment $attachment): Response
    {
        $this->authorize('uploadAttachment', $lead);
        abort_unless($attachment->lead_id === $lead->id, 404);
        abort_unless(Storage::disk('public')->exists($attachment->file_path), 404);

        return Storage::disk('public')->response(
            $attachment->file_path,
            $attachment->file_original_name,
            ['Content-Type' => $attachment->file_type]
        );
    }

    public function download(Lead $lead, LeadAttachment $attachment): Response
    {
        $this->authorize('uploadAttachment', $lead);
        abort_unless($attachment->lead_id === $lead->id, 404);
        abort_unless(Storage::disk('public')->exists($attachment->file_path), 404);

        return Storage::disk('public')->download($attachment->file_path, $attachment->file_original_name);
    }

    public function destroy(Lead $lead, LeadAttachment $attachment): RedirectResponse
    {
        $this->authorize('uploadAttachment', $lead);
        abort_unless($attachment->lead_id === $lead->id, 404);

        DB::transaction(function () use ($lead, $attachment): void {
            $originalName = $attachment->file_original_name;

            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            $attachment->delete();

            $lead->updated_by = auth()->id();
            $lead->save();

            $this->activityLogger->logAttachmentDeleted($lead, $originalName, auth()->user());
        });

        return redirect()
            ->route('crm.leads.show', $lead)
            ->with('crm_success', 'Attachment deleted successfully.');
    }
}
