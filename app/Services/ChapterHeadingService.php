<?php

namespace App\Services;

use App\Contracts\ServiceInterface;
use App\Exceptions\ChapterHeadingService\ChapterHeadingNotFoundException;
use App\Exceptions\ChapterHeadingService\ChapterHeadingOwnerMismatchedException;
use App\Models\ChapterHeading;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
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
        return ChapterHeading::paginate(100);
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
    }

    /**
     * Getting others ChapterHeadings
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getChapterHeadings(): Collection
    {
        return ChapterHeading::whereBetween('code_category_id', [50, 63])->get();
    }
}
