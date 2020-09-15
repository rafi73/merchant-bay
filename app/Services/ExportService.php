<?php

namespace App\Services;

use App\Contracts\ServiceInterface;
use App\Exceptions\ExportService\ExportNotFoundException;
use App\Exceptions\ExportService\ExportOwnerMismatchedException;
use App\Models\Country;
use App\Models\Export;
use Illuminate\Pagination\LengthAwarePaginator;

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
    }

    /**
     * Getting others Exports
     *
     * @return array
     */
    public function getExportData(int $chapterHeadingId): array
    {
        $countries = Country::whereHas('exports', function ($query) use ($chapterHeadingId) {
            return $query->where('chapter_heading_id', '=', $chapterHeadingId);
        })->get();

        $lineData = Export::where('chapter_heading_id', $chapterHeadingId)
            ->orderBy('fiscal_year')
            ->select('fiscal_year', 'usd')
            ->get();

        $lineMap = new \Ds\Map();
        foreach ($lineData as $key => $value) {
            $usdInMillion = $value->usd / 1000000;
            $sum = $lineMap->hasKey($value->fiscal_year) ? $lineMap->get($value->fiscal_year) + $usdInMillion : $usdInMillion;
            $lineMap->put($value->fiscal_year, $sum);
        }

        return [
            'countries' => $countries,
            'fiscal_years' => $lineMap->keys(),
            'usd' => [$lineMap->values()],
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
            ->orderBy('fiscal_year')
            ->select('fiscal_year', 'usd')
            ->get();

        $lineMap = new \Ds\Map();
        foreach ($lineData as $key => $value) {
            $usdInMillion = $value->usd / 1000000;
            $sum = $lineMap->hasKey($value->fiscal_year) ? $lineMap->get($value->fiscal_year) + $usdInMillion : $usdInMillion;
            $lineMap->put($value->fiscal_year, $sum);
        }

        return [
            'fiscal_years' => $lineMap->keys(),
            'usd' => [$lineMap->values()],
        ];
    }
}
