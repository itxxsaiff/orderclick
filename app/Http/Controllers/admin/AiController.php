<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Services\AiAssistant;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function assist(Request $request, AiAssistant $ai)
    {
        $request->validate([
            'action' => 'required|string',
            'text'   => 'nullable|string|max:8000',
        ]);

        $result = $ai->assist(
            $request->input('action'),
            (string) $request->input('text', ''),
            [
                'field'       => $request->input('field', 'content'),
                'target_lang' => $request->input('target_lang', 'English'),
                'context'     => $request->input('context', ''),
            ]
        );

        return response()->json($result, $result['success'] ? 200 : 422);
    }
}
