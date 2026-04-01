<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralDocuments;
use Illuminate\Http\Request;
use App\Models\Attachment;
use App\Models\User;
use App\Models\Role;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {

        $totalUsers = User::where('role_id','!=', 1)->count();

        // Get users by role
        $usersByRole = [];
        $roles = Role::withCount('users')->get();
        foreach ($roles as $role) {
            $usersByRole[$role->name] = $role->users_count;
        }


        // Get recent users
        $recentUsers = User::with('role')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();



        return view('admin.dashboard', compact(

            'totalUsers',
            'usersByRole',
            'recentUsers',
        ));
    }

    /**
     * Display system statistics.
     */
    public function statistics()
    {
        // More detailed statistics
        $stats = [
            'files_by_type' => $this->getFilesByType(),
            'users_by_status' => $this->getUsersByStatus(),
            'monthly_uploads' => $this->getMonthlyUploads(),
        ];

        return view('admin.statistics', compact('stats'));
    }

    /**
     * Get files grouped by type.
     */
    private function getFilesByType()
    {
        return Attachment::selectRaw('SUBSTRING_INDEX(original_name, ".", -1) as extension, COUNT(*) as count')
            ->groupBy('extension')
            ->orderByDesc('count')
            ->get();
    }

    /**
     * Get users grouped by status/role.
     */
    private function getUsersByStatus()
    {
        return User::selectRaw('roles.name as role_name, COUNT(*) as count')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->groupBy('roles.name')
            ->get();
    }

    /**
     * Get monthly upload statistics.
     */
    private function getMonthlyUploads()
    {
        return Attachment::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(6)
            ->get();
    }
}
