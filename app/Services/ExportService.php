<?php

namespace App\Services;

use App\Contracts\ServiceInterface;
use App\Exceptions\ExportService\ExportBulkDataErrorException;
use App\Exceptions\ExportService\ExportBulkStructureException;
use App\Exceptions\ExportService\ExportNotFoundException;
use App\Exceptions\ExportService\ExportOwnerMismatchedException;
use App\Models\Export;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ExportService implements ServiceInterface
{
    /**
     * Get all Exports.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAll(): LengthAwarePaginator
    {
        return Export::paginate(10);
    }

    /**
     * Create & store a new robot
     *
     * @param array $request
     * @return \App\Export
     */
    public function create(array $request): Export
    {
        //dd($request);
        return Export::create($request);
    }

    /**
     * Delete a Export by id
     *
     * @param int $id
     * @throws \App\Exceptions\ExportService\ExportNotFoundException
     * @throws \App\Exceptions\ExportService\ExportOwnerMismatchedException
     * @return bool
     */
    public function delete(int $id): bool
    {
        $robot = Export::find($id);
        if (!$robot) {
            throw new ExportNotFoundException();
        }
        if ($robot->user_id != Auth::id()) {
            throw new ExportOwnerMismatchedException();
        }

        return Export::destroy($id);
    }

    /**
     * Update a Export.
     *
     * @param array $request
     * @param int $id
     * @throws \App\Exceptions\ExportService\ExportNotFoundException
     * @throws \App\Exceptions\ExportService\ExportOwnerMismatchedException
     * @return \App\Export
     */
    public function update(array $request, int $id): Export
    {
        $robot = Export::find($id);
        if (!$robot) {
            throw new ExportNotFoundException();
        }
        if ($robot->user_id != Auth::id()) {
            throw new ExportOwnerMismatchedException();
        }

        return tap(Export::findOrFail($id))->update($request)->fresh();
    }

    /**
     * Find a Export by id
     *
     * @param int $id
     * @throws \App\Exceptions\ExportService\ExportNotFoundException
     * @return \App\Export
     */
    public function find(int $id): Export
    {
        $robot = Export::find($id);
        if (!$robot) {
            throw new ExportNotFoundException();
        }
        return $robot;
    }

    /**
     * Create & store robots from CSV
     *
     * @param array $request
     * @throws \App\Exceptions\ExportService\ExportBulkStructureException
     * @throws \App\Exceptions\ExportService\ExportBulkDataErrorException
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
            throw new ExportBulkStructureException();
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
            Export::insert($robots);
        } catch (QueryException $exception) {
            throw new ExportBulkDataErrorException();
        }
        return true;
    }

    /**
     * Getting own Exports
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getOwnExports(): Collection
    {
        return Auth::user()->robots;
    }

    /**
     * Getting others Exports
     *
     * @return array
     */
    public function getExportData(int $chapterHeadingId): array
    {
        $graphData = Export::where('chapter_heading_id', $chapterHeadingId);

        $countries = $graphData->with('country')->get()->toArray();
        $lineData = $graphData->distinct()->orderBy('fiscal_year')->select('fiscal_year', 'usd')->get()->toArray();

        return [
            'countries' => array_column($countries, 'country'),
            'fiscal_years' => array_column($lineData, 'fiscal_year'),
            'usd' => [array_column($lineData, 'usd')],
        ];
    }

    /**
     * Getting others Exports
     *
     * @return array
     */
    public function getExportDataByCountry(int $chapterHeadingId, int $countryId): array
    {
        $lineData = Export::where('country_id', $countryId)
            ->where('chapter_heading_id', $chapterHeadingId)
            ->distinct()->orderBy('fiscal_year')
            ->select('fiscal_year', 'usd')
            ->get()
            ->toArray();

        return [
            'fiscal_years' => array_column($lineData, 'fiscal_year'),
            'usd' => [array_column($lineData, 'usd')],
        ];
    }
}
