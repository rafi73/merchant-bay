<?php

namespace App\Http\Controllers;

use App\Http\Resources\CodeCategoryResource;
use App\Models\CodeCategory;
use App\Services\CodeCategoryService;
use Illuminate\Http\Request;

class CodeCategoryController extends Controller
{
    /**
     * The CodeCategory Service instance.
     *
     * @var CodeCategoryService
     */
    protected $codeCategoryService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CodeCategoryService $codeCategoryService = null)
    {
        $this->codeCategoryService = $codeCategoryService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $codeCategorys = $this->codeCategoryService->getAll();
        return CodeCategoryResource::collection($codeCategorys);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * @param  \App\Models\CodeCategory  $codeCategory
     * @return \Illuminate\Http\Response
     */
    public function show(CodeCategory $codeCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CodeCategory  $codeCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(CodeCategory $codeCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CodeCategory  $codeCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CodeCategory $codeCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CodeCategory  $codeCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(CodeCategory $codeCategory)
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCodeCategories()
    {
        $codeCategories = $this->codeCategoryService->getCodeCategories();
        return CodeCategoryResource::collection($codeCategories);
    }
}
