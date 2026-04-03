<?php

namespace App\Http\Controllers;

use App\Models\FeeStructure;
use Illuminate\Http\Request;

class FeeStructureController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\FeeStructure::with(['class', 'type']);

        if ($request->school_class_id) {
            $query->where('school_class_id', $request->school_class_id);
        }

        $fees = $query->get()->map(function ($fee) {
            return [
                'id' => $fee->id,
                'class' => $fee->class->name,
                'fee_type' => $fee->type->name,
                'title' => $fee->name,
                'amount' => $fee->amount,
            ];
        });

        return response()->json([
            'data' => $fees
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'fee_type_id' => 'required|exists:fee_types,id',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0'
        ]);

        $fee = FeeStructure::create([
            'school_class_id' => $request->school_class_id,
            'fee_type_id' => $request->fee_type_id,
            'name' => $request->name,
            'amount' => $request->amount,
        ]);

        return response()->json([
            'message' => 'Fee Structure Created',
            'data' => $fee
        ]);
    }
}
