<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqResource;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FaqController extends Controller
{
    /**
     * Display a listing of FAQs.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $faqs = Faq::query()
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 15));

        return FaqResource::collection($faqs);
    }
}
