<?php
namespace App\Http\Controllers;

use App\Services\StudentService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    protected $service;

    public function __construct(StudentService $service)
    {
        $this->service = $service;
        $this->middleware('permission:create student')->only('store');
        $this->middleware('permission:view student')->only('index');
    }

    public function store(Request $request)
    {
        $student = $this->service->create($request->all());

        return response()->json([
            'message' => 'Student created',
            'data' => $student
        ]);
    }

    public function index()
    {
        return $this->service->all();
    }
}
