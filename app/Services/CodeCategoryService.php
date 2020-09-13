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
        return CodeCategory::create($request); 
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
        $robot = CodeCategory::find($id);
        if(!$robot)
        {
            throw new CodeCategoryNotFoundException();
        }
        if($robot->user_id != Auth::id())
        {
            throw new CodeCategoryOwnerMismatchedException();
        }

        return CodeCategory::destroy($id);
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
        $robot = CodeCategory::find($id);
        if(!$robot)
        {
            throw new CodeCategoryNotFoundException();
        }
        if($robot->user_id != Auth::id())
        {
            throw new CodeCategoryOwnerMismatchedException();
        }

        return tap(CodeCategory::findOrFail($id))->update($request)->fresh();
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
        $robot = CodeCategory::find($id);
        if(!$robot)
        {
            throw new CodeCategoryNotFoundException();
        }
        return $robot;
    }

    /**
     * Create & store robots from CSV
     *
     * @param array $request
     * @throws \App\Exceptions\CodeCategoryService\CodeCategoryBulkStructureException
     * @throws \App\Exceptions\CodeCategoryService\CodeCategoryBulkDataErrorException
     * @return bool
     */
    public function createBulk(array $request) : bool
    {
        $requiredStructure = ['name', 'power', 'speed', 'weight'];
        $file = $request['file'];
        $lines = explode("\n", file_get_contents($file));
        $head = str_getcsv(array_shift($lines));
        sort($head);

        if($head !== $requiredStructure)
        {
            throw new CodeCategoryBulkStructureException();
        }

        $robots = [];
        for ($i = 0; $i < count($lines); $i++) 
        { 
            if(!strlen($lines[$i])) continue;
            $robot = array_combine($head, str_getcsv($lines[$i]));
            $robot['created_by'] = $robot['updated_by'] = $robot['user_id'] = Auth::id();
            $robot['created_at'] = $robot['updated_at'] = now();
            $robots[] = $robot;
        }
        
        try 
        {
            CodeCategory::insert($robots);
        }
        catch(QueryException $exception)
        {
            throw new CodeCategoryBulkDataErrorException();
        }
        return true;
    }

    /**
     * Getting own CodeCategorys
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getOwnCodeCategorys() : Collection
    {
        return Auth::user()->robots;
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
