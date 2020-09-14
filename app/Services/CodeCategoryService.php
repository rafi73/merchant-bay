<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\CodeCategory;
use Illuminate\Support\Facades\Auth;
use App\Contracts\ServiceInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exceptions\CodeCategoryService\CodeCategoryNotFoundException;
use App\Exceptions\CodeCategoryService\CodeCategoryBulkDataErrorException;
use App\Exceptions\CodeCategoryService\CodeCategoryBulkStructureException;
use App\Exceptions\CodeCategoryService\CodeCategoryOwnerMismatchedException;



class CodeCategoryService implements ServiceInterface
{
    /**
     * Get all CodeCategorys.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAll() : LengthAwarePaginator
    {
        return CodeCategory::paginate(10);
    }

    /**
     * Create & store a new robot
     *
     * @param array $request
     * @return \App\CodeCategory
     */
    public function create(array $request) : CodeCategory
    {
    }

    /**
     * Delete a CodeCategory by id
     *
     * @param int $id
     * @throws \App\Exceptions\CodeCategoryService\CodeCategoryNotFoundException
     * @throws \App\Exceptions\CodeCategoryService\CodeCategoryOwnerMismatchedException
     * @return bool
     */
    public function delete(int $id) : bool
    {
    }

    /**
     * Update a CodeCategory.
     *
     * @param array $request
     * @param int $id
     * @throws \App\Exceptions\CodeCategoryService\CodeCategoryNotFoundException
     * @throws \App\Exceptions\CodeCategoryService\CodeCategoryOwnerMismatchedException
     * @return \App\CodeCategory
     */
    public function update(array $request, int $id) : CodeCategory
    {
    }

    /**
     * Find a CodeCategory by id
     *
     * @param int $id
     * @throws \App\Exceptions\CodeCategoryService\CodeCategoryNotFoundException
     * @return \App\CodeCategory
     */
    public function find(int $id) : CodeCategory
    {
    }

    /**
     * Getting others CodeCategorys
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getCodeCategories() : Collection
    {
        return CodeCategory::all();
    }
}
