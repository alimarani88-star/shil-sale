<?php

namespace App\Services;

interface ShiliranApiInterface
{
    public function getMainGroups(): array;

    public function getGroups(): array;

    public function getGroupById(int $id): array;

    public function getItems(): array;

    public function getItemById(int $id): array;

    public function getGroupsByMainGroupId(int $id): array;

    public function getItemsByGroupId(int $id): array;

    public function getMainGroupById(int $id): array;
}

