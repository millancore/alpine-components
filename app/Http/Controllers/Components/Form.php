<?php

namespace App\Http\Controllers\Components;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Form extends Controller
{
    public function __invoke(Request $request)
    {
        $method = $request->input('method');

        if (!method_exists($this, $method)) {
            return response()->json(['error' => 'Invalid Method'], 400);
        }

        return $this->{$method}($request);
    }

    private function render(Request $request) : JsonResponse
    {
        $view = $request->input('view');

        $users = ['Juan', 'Maria', 'Pedro'];


        $render = match ($view) {
            'list' => view('components.list', compact('users'))
            // others views
        };

        return response()->json([
            'html' => $render->render()
        ]);
    }

    private function save(Request $request) : JsonResponse
    {
      return response()->json(true);
    }

}
