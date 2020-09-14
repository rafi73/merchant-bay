<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChapterHeadingRequest;
use App\Http\Resources\ChapterHeadingResource;
use App\Models\ChapterHeading;
use App\Services\ChapterHeadingService;
use Illuminate\Http\Request;

class ChapterHeadingController extends Controller
{
    /**
     * The ChapterHeading Service instance.
     *
     * @var ChapterHeadingService
     */
    protected $chapterHeadingService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ChapterHeadingService $chapterHeadingService = null)
    {
        $this->chapterHeadingService = $chapterHeadingService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $chapterHeadings = $this->chapterHeadingService->getAll();
        return ChapterHeadingResource::collection($chapterHeadings);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(ChapterHeadingRequest $request): ChapterHeadingResource
    {
        $chapterHeading = $this->chapterHeadingService->create($request->all());
        return new ChapterHeadingResource($chapterHeading);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ChapterHeading  $chapterHeading
     * @return \Illuminate\Http\Response
     */
    public function show(ChapterHeading $chapterHeading)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ChapterHeading  $chapterHeading
     * @return \Illuminate\Http\Response
     */
    public function edit(ChapterHeading $chapterHeading)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ChapterHeading  $chapterHeading
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ChapterHeading $chapterHeading)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ChapterHeading  $chapterHeading
     * @return \Illuminate\Http\Response
     */
    public function destroy(ChapterHeading $chapterHeading)
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getChapterHeadings()
    {
        $chapterHeadings = $this->chapterHeadingService->getChapterHeadings();
        return ChapterHeadingResource::collection($chapterHeadings);
    }
}
