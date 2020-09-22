<?php

namespace App\Services;

use App\Contracts\ServiceInterface;
use App\Exceptions\ChapterHeadingService\ChapterHeadingNotFoundException;
use App\Exceptions\ChapterHeadingService\ChapterHeadingOwnerMismatchedException;
use App\Models\ChapterHeading;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Intervention\Image\ImageManagerStatic as Image;

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
        $image = $request['image'];
        $imageName = round(microtime(true) * 1000) . '.' . $image->getClientOriginalExtension();

        $path = storage_path('uploads');
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $imageResize = Image::make($image->getRealPath());
        $callback = function ($constraint) {$constraint->upsize();};
        $imageResize->widen(1000, $callback)->heighten(1000, $callback);
        $imageResize->save($path . '/' . $imageName);

        $request['image'] = $imageName;
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
