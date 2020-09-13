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
        $robot = Country::find($id);
        if(!$robot)
        {
            throw new CountryNotFoundException();
        }
        if($robot->user_id != Auth::id())
        {
            throw new CountryOwnerMismatchedException();
        }

        return Country::destroy($id);
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
        $robot = Country::find($id);
        if(!$robot)
        {
            throw new CountryNotFoundException();
        }
        if($robot->user_id != Auth::id())
        {
            throw new CountryOwnerMismatchedException();
        }

        return tap(Country::findOrFail($id))->update($request)->fresh();
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
        $robot = Country::find($id);
        if(!$robot)
        {
            throw new CountryNotFoundException();
        }
        return $robot;
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
        $requiredStructure = ['name', 'power', 'speed', 'weight'];
        $file = $request['file'];
        $lines = explode("\n", file_get_contents($file));
        $head = str_getcsv(array_shift($lines));
        sort($head);

        if($head !== $requiredStructure)
        {
            throw new CountryBulkStructureException();
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
            Country::insert($robots);
        }
        catch(QueryException $exception)
        {
            throw new CountryBulkDataErrorException();
        }
        return true;
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
