<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use App\Contracts\ServiceInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exceptions\SupplierService\SupplierNotFoundException;
use App\Exceptions\SupplierService\SupplierBulkDataErrorException;
use App\Exceptions\SupplierService\SupplierBulkStructureException;
use App\Exceptions\SupplierService\SupplierOwnerMismatchedException;



class SupplierService implements ServiceInterface
{
    /**
     * Get all Suppliers.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAll() : LengthAwarePaginator
    {
        return Supplier::paginate(10);
    }

    /**
     * Create & store a new robot
     *
     * @param array $request
     * @return \App\Supplier
     */
    public function create(array $request) : Supplier
    {
        return Supplier::create($request); 
    }

    /**
     * Delete a Supplier by id
     *
     * @param int $id
     * @throws \App\Exceptions\SupplierService\SupplierNotFoundException
     * @throws \App\Exceptions\SupplierService\SupplierOwnerMismatchedException
     * @return bool
     */
    public function delete(int $id) : bool
    {
        $robot = Supplier::find($id);
        if(!$robot)
        {
            throw new SupplierNotFoundException();
        }
        if($robot->user_id != Auth::id())
        {
            throw new SupplierOwnerMismatchedException();
        }

        return Supplier::destroy($id);
    }

    /**
     * Update a Supplier.
     *
     * @param array $request
     * @param int $id
     * @throws \App\Exceptions\SupplierService\SupplierNotFoundException
     * @throws \App\Exceptions\SupplierService\SupplierOwnerMismatchedException
     * @return \App\Supplier
     */
    public function update(array $request, int $id) : Supplier
    {
        $robot = Supplier::find($id);
        if(!$robot)
        {
            throw new SupplierNotFoundException();
        }
        if($robot->user_id != Auth::id())
        {
            throw new SupplierOwnerMismatchedException();
        }

        return tap(Supplier::findOrFail($id))->update($request)->fresh();
    }

    /**
     * Find a Supplier by id
     *
     * @param int $id
     * @throws \App\Exceptions\SupplierService\SupplierNotFoundException
     * @return \App\Supplier
     */
    public function find(int $id) : Supplier
    {
        $robot = Supplier::find($id);
        if(!$robot)
        {
            throw new SupplierNotFoundException();
        }
        return $robot;
    }

    /**
     * Create & store robots from CSV
     *
     * @param array $request
     * @throws \App\Exceptions\SupplierService\SupplierBulkStructureException
     * @throws \App\Exceptions\SupplierService\SupplierBulkDataErrorException
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
            throw new SupplierBulkStructureException();
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
            Supplier::insert($robots);
        }
        catch(QueryException $exception)
        {
            throw new SupplierBulkDataErrorException();
        }
        return true;
    }

    /**
     * Getting own Suppliers
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getOwnSuppliers() : Collection
    {
        return Auth::user()->robots;
    }

    /**
     * Getting others Suppliers
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getSuppliers() : Collection
    {
        return Supplier::all();
    }
}
