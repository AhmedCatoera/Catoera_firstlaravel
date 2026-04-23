<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentAttachment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IncidentAttachmentController extends Controller
{
    public function store(Request $request, Incident $incident): RedirectResponse
    {
        $user = $request->user();
        if ($user->isStaff()) {
            $teamIds = $user->associatedTeamIds();
            if (! (
                $teamIds->isNotEmpty()
                && $incident->assignment
                && in_array($incident->assignment->team_id, $teamIds->all(), true)
            )) {
                return redirect()->route('incidents.show', $incident)
                    ->with('error', 'You can only upload attachments for incidents assigned to your team.');
            }
        }

        $data = $request->validate([
            'attachment' => ['required', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,txt'],
        ]);

        $file = $data['attachment'];
        $path = $file->store('incident-attachments', 'public');

        $attachment = $incident->attachments()->create([
            'uploaded_by' => $user->id,
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize() ?: 0,
        ]);

        $incident->logActivity(
            event: 'attachment_uploaded',
            details: 'Attachment uploaded: '.$attachment->original_name,
            userId: $user->id,
        );

        return back()->with('success', 'Attachment uploaded.');
    }

    public function download(Incident $incident, IncidentAttachment $attachment): StreamedResponse
    {
        abort_unless($attachment->incident_id === $incident->id, 404);

        return Storage::disk('public')->download($attachment->file_path, $attachment->original_name);
    }

    public function destroy(Request $request, Incident $incident, IncidentAttachment $attachment): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            return redirect()->route('incidents.show', $incident)
                ->with('error', 'Only admin can delete attachments.');
        }
        abort_unless($attachment->incident_id === $incident->id, 404);

        Storage::disk('public')->delete($attachment->file_path);
        $name = $attachment->original_name;
        $attachment->delete();

        $incident->logActivity(
            event: 'attachment_deleted',
            details: 'Attachment removed: '.$name,
            userId: $request->user()->id,
        );

        return back()->with('success', 'Attachment deleted.');
    }
}
