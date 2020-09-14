<?php

namespace App\Services;

use App\Contracts\ServiceInterface;
use App\Exceptions\ChapterHeadingService\ChapterHeadingBulkDataErrorException;
use App\Exceptions\ChapterHeadingService\ChapterHeadingBulkStructureException;
use App\Exceptions\ChapterHeadingService\ChapterHeadingNotFoundException;
use App\Exceptions\ChapterHeadingService\ChapterHeadingOwnerMismatchedException;
use App\Models\ChapterHeading;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Storage;

class ChapterHeadingService implements ServiceInterface
{
    /**
     * Get all ChapterHeadings.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAll(): LengthAwarePaginator
    {
        return ChapterHeading::paginate(10);
    }

    /**
     * Create & store a new robot
     *
     * @param array $request
     * @return \App\ChapterHeading
     */
    public function create(array $request): ChapterHeading
    {
        $base64_image = $request['image'];
        @list($type, $file_data) = explode(';', $base64_image);
        @list(, $file_data) = explode(',', $file_data);
        $imageName = Str::random(10) . '.' . 'png';
        Storage::disk('local')->put($imageName, base64_decode($file_data));
        $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        //dd($storagePath);
        $request['image'] = $storagePath;
        return ChapterHeading::create($request);
    }

    /**
     * Delete a ChapterHeading by id
     *
     * @param int $id
     * @throws \App\Exceptions\ChapterHeadingService\ChapterHeadingNotFoundException
     * @throws \App\Exceptions\ChapterHeadingService\ChapterHeadingOwnerMismatchedException
     * @return bool
     */
    public function delete(int $id): bool
    {
        $robot = ChapterHeading::find($id);
        if (!$robot) {
            throw new ChapterHeadingNotFoundException();
        }
        if ($robot->user_id != Auth::id()) {
            throw new ChapterHeadingOwnerMismatchedException();
        }

        return ChapterHeading::destroy($id);
    }

    /**
     * Update a ChapterHeading.
     *
     * @param array $request
     * @param int $id
     * @throws \App\Exceptions\ChapterHeadingService\ChapterHeadingNotFoundException
     * @throws \App\Exceptions\ChapterHeadingService\ChapterHeadingOwnerMismatchedException
     * @return \App\ChapterHeading
     */
    public function update(array $request, int $id): ChapterHeading
    {
        $robot = ChapterHeading::find($id);
        if (!$robot) {
            throw new ChapterHeadingNotFoundException();
        }
        if ($robot->user_id != Auth::id()) {
            throw new ChapterHeadingOwnerMismatchedException();
        }

        return tap(ChapterHeading::findOrFail($id))->update($request)->fresh();
    }

    /**
     * Find a ChapterHeading by id
     *
     * @param int $id
     * @throws \App\Exceptions\ChapterHeadingService\ChapterHeadingNotFoundException
     * @return \App\ChapterHeading
     */
    public function find(int $id): ChapterHeading
    {
        $robot = ChapterHeading::find($id);
        if (!$robot) {
            throw new ChapterHeadingNotFoundException();
        }
        return $robot;
    }

    /**
     * Create & store robots from CSV
     *
     * @param array $request
     * @throws \App\Exceptions\ChapterHeadingService\ChapterHeadingBulkStructureException
     * @throws \App\Exceptions\ChapterHeadingService\ChapterHeadingBulkDataErrorException
     * @return bool
     */
    public function createBulk(array $request): bool
    {
        $requiredStructure = ['name', 'power', 'speed', 'weight'];
        $file = $request['file'];
        $lines = explode("\n", file_get_contents($file));
        $head = str_getcsv(array_shift($lines));
        sort($head);

        if ($head !== $requiredStructure) {
            throw new ChapterHeadingBulkStructureException();
        }

        $robots = [];
        for ($i = 0; $i < count($lines); $i++) {
            if (!strlen($lines[$i])) {
                continue;
            }

            $robot = array_combine($head, str_getcsv($lines[$i]));
            $robot['created_by'] = $robot['updated_by'] = $robot['user_id'] = Auth::id();
            $robot['created_at'] = $robot['updated_at'] = now();
            $robots[] = $robot;
        }

        try
        {
            ChapterHeading::insert($robots);
        } catch (QueryException $exception) {
            throw new ChapterHeadingBulkDataErrorException();
        }
        return true;
    }

    /**
     * Getting own ChapterHeadings
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getOwnChapterHeadings(): Collection
    {
        return Auth::user()->robots;
    }

    /**
     * Getting others ChapterHeadings
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getOtherChapterHeadings(): Collection
    {
        return ChapterHeading::where('user_id', '<>', Auth::id())->get();
    }
}
