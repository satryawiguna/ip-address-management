<?php

namespace App\Repository;

use App\Domain\User;
use App\Repository\Contract\IUserRepository;

class UserRepository extends BaseRepository implements IUserRepository
{
    public User $user;

    public function __construct(User $user)
    {
        parent::__construct($user);
        $this->user = $user;
    }

    public function allSearch(string $keyword)
    {
        $parameter = $this->getParameter($keyword);

        return $this->user->whereRaw("(name LIKE ? OR email LIKE ?)", $parameter);
    }

    public function allSearchPage(string $keyword, int $perPage, int $page)
    {
        $parameter = $this->getParameter($keyword);

        return $this->user->whereRaw("(name LIKE ? OR email LIKE ?)", $parameter)
            ->simplePaginate(perPage: $perPage, page: $page);
    }

    private function getParameter(string $keyword) {
        return [
            'name' => '%' . $keyword . '%',
            'email' => '%' . $keyword . '%'
        ];
    }
}
