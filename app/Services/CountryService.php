<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Country;
use Illuminate\Support\Facades\Auth;
use App\Contracts\ServiceInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exceptions\CountryService\CountryNotFoundException;
use App\Exceptions\CountryService\CountryBulkDataErrorException;
use App\Exceptions\CountryService\CountryBulkStructureException;
use App\Exceptions\CountryService\CountryOwnerMismatchedException;



class CountryService implements ServiceInterface
{
    /**
     * Get all Countrys.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAll() : LengthAwarePaginator
    {
        return Country::paginate(10);
    }

    /**
     * Create & store a new robot
     *
     * @param array $request
     * @return \App\Country
     */
    public function create(array $request) : Country
    {
        return Country::create($request); 
    }

    /**
     * Delete a Country by id
     *
     * @param int $id
     * @throws \App\Exceptions\CountryService\CountryNotFoundException
     * @throws \App\Exceptions\CountryService\CountryOwnerMismatchedException
     * @return bool
     */
    public function delete(int $id) : bool
    {
    }

    /**
     * Update a Country.
     *
     * @param array $request
     * @param int $id
     * @throws \App\Exceptions\CountryService\CountryNotFoundException
     * @throws \App\Exceptions\CountryService\CountryOwnerMismatchedException
     * @return \App\Country
     */
    public function update(array $request, int $id) : Country
    {
    }

    /**
     * Find a Country by id
     *
     * @param int $id
     * @throws \App\Exceptions\CountryService\CountryNotFoundException
     * @return \App\Country
     */
    public function find(int $id) : Country
    {
    }

    /**
     * Create & store robots from CSV
     *
     * @param array $request
     * @throws \App\Exceptions\CountryService\CountryBulkStructureException
     * @throws \App\Exceptions\CountryService\CountryBulkDataErrorException
     * @return bool
     */
    public function createBulk(array $request) : bool
    {
    }

    /**
     * Getting own Countrys
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getOwnCountrys() : Collection
    {
        return Auth::user()->robots;
    }

    /**
     * Getting others Countrys
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getCountries() : Collection
    {
        return Country::all();
    }
}
