<?php

namespace App\Services;

use App\Contracts\ServiceInterface;
use App\Exceptions\SupplierService\SupplierBulkDataErrorException;
use App\Exceptions\SupplierService\SupplierBulkStructureException;
use App\Exceptions\SupplierService\SupplierNotFoundException;
use App\Exceptions\SupplierService\SupplierOwnerMismatchedException;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class SupplierService implements ServiceInterface
{
    /**
     * Get all Suppliers.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAll(): LengthAwarePaginator
    {
        return Supplier::paginate(10);
    }

    /**
     * Create & store a new robot
     *
     * @param array $request
     * @return \App\Supplier
     */
    public function create(array $request): Supplier
    {
    }

    /**
     * Delete a Supplier by id
     *
     * @param int $id
     * @throws \App\Exceptions\SupplierService\SupplierNotFoundException
     * @throws \App\Exceptions\SupplierService\SupplierOwnerMismatchedException
     * @return bool
     */
    public function delete(int $id): bool
    {
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
    public function update(array $request, int $id): Supplier
    {
    }

    /**
     * Find a Supplier by id
     *
     * @param int $id
     * @throws \App\Exceptions\SupplierService\SupplierNotFoundException
     * @return \App\Supplier
     */
    public function find(int $id): Supplier
    {
    }

    /**
     * Getting others Suppliers
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getSuppliers(): Collection
    {
        return Supplier::take(10)->get();
    }
}
