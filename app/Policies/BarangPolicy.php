<?php

namespace App\Policies;

use App\Models\Barang;
use App\Models\User;

class BarangPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function view(User $user, Barang $barang): bool
    {
        return $barang->user_id === $user->id;
    }

    public function update(User $user, Barang $barang): bool
    {
        return $barang->user_id === $user->id;
    }

    public function delete(User $user, Barang $barang): bool
    {
        return $barang->user_id === $user->id;
    }
}
