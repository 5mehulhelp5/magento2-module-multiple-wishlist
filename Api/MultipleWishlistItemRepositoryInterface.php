<?php

declare(strict_types=1);

namespace BrunoDuarte\MultipleWishlist\Api;

use BrunoDuarte\MultipleWishlist\Api\Data\MultipleWishlistItemInterface;

interface MultipleWishlistItemRepositoryInterface
{
    public function getById(int $multipleWishlistId): ?MultipleWishlistItemInterface;

    public function save(MultipleWishlistItemInterface $multipleWishlistItem): MultipleWishlistItemInterface;

    // public function delete(MultipleWishlistInterface $multipleWishlist): bool;

    // public function deleteById(int $multipleWishlistId): bool;
}
