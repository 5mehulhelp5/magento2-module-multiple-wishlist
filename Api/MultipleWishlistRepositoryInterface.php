<?php

declare(strict_types=1);

namespace BrunoDuarte\MultipleWishlist\Api;

use BrunoDuarte\MultipleWishlist\Api\Data\MultipleWishlistInterface;

interface MultipleWishlistRepositoryInterface
{
    public function getById(int $multipleWishlistId): MultipleWishlistInterface;

    public function save(MultipleWishlistInterface $multipleWishlist): MultipleWishlistInterface;

    public function update(array $multipleWishlistFormData): bool;

    public function delete(MultipleWishlistInterface $multipleWishlist): bool;

    public function deleteById(int $multipleWishlistId): bool;
}
