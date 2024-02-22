<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessSubmission;
use Illuminate\Http\Request;
use App\Models\Submission;

class SubmissionController extends Controller
{
    public function submit(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        ProcessSubmission::dispatch($request->only(['name', 'email', 'message']));

        return response()->json(['message' => 'Submission received.'], 200);
    }
}