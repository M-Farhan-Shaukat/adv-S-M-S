<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\FinalForm;
use App\Models\GeneralDocuments;
use App\Models\UserDownloadedDocuments;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Attachment;
use App\Models\UserDocument;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        try {

            return view('user.dashboard');

        } catch (\Exception $e) {
            // If there's an error, show basic dashboard
            \Log::error('Dashboard error: ' . $e->getMessage());

            return view('user.dashboard', [
                'attachments' => [],
                'userDocuments' => [],
                'uploadedCount' => 0,
                'pendingCount' => 0,
                'approvedCount' => 0
            ]);
        }
    }

    public function status()
    {
        return view('user.status');
    }

    public function track()
    {
        return view('user.track');
    }

    public function downloadAgreement()
    {
        $user = auth()->user();
        $pdf = \PDF::loadView('documents.agreement', [
            'user' => $user
        ]);
        return $pdf->download('User_Agreement.pdf');
    }

    /**
     * Download User-specific Challan
     */
    public function downloadChallan()
    {
        $user = auth()->user();

        // Generate unique ID
        $uniqueId = strtoupper(uniqid('CH-'));

        // Set expiry (next 3 working days)
        $expiryDate = $this->calculateExpiry(3);

        // Path to save generated challan PDF
        $fileName = "Challan_{$user->id}_{$uniqueId}.pdf";
        $filePath = storage_path("app/public/challans/{$fileName}");

        // Ensure folder exists
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        // Use a blade template to generate PDF
        $pdf =\Pdf::loadView('documents.challan', [
            'user' => $user,
            'uniqueId' => $uniqueId,
            'expiryDate' => $expiryDate,
        ]);

        $pdf->save($filePath);

        // Store record in user_downloaded_documents
        DB::table('user_downloaded_documents')->insert([
            'user_id' => $user->id,
            'document_type' => 'challan',
            'unique_id' => $uniqueId,
            'downloaded_at' => now(),
            'expiry_date' => $expiryDate,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->download($filePath, "Challan_{$uniqueId}.pdf", [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Calculate expiry date in next N working days
     */
    private function calculateExpiry($days)
    {
        $date = Carbon::now();
        $added = 0;

        while ($added < $days) {
            $date->addDay();
            if (!in_array($date->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])) {
                $added++;
            }
        }

        return $date->format('Y-m-d');
    }
}
