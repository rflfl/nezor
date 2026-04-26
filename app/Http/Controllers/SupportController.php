<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class SupportController extends Controller
{
    public function __invoke(): Response
    {
        $markdownPath = resource_path('markdown/support.md');
        $markdown = File::exists($markdownPath)
            ? File::get($markdownPath)
            : '# Suporte\n\nDocumentação não encontrada.';

        $html = Str::markdown($markdown, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        return Inertia::render('Support', [
            'content' => $html,
        ]);
    }
}
