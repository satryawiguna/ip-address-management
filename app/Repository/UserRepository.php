<?php

namespace App\Repository;

use App\Application\Request\Auth\RegisterDataRequest;
use App\Core\Domain\BaseEntity;
use App\Domain\User;
use App\Repository\Contract\IUserRepository;
use Illuminate\Support\Collection;

class UserRepository extends BaseRepository implements IUserRepository
{
    public User $user;

    public function __construct(User $user)
    {
        parent::__construct($user);
        $this->user = $user;
    }

    public function allSearch(string $keyword): Collection
    {
        $parameter = $this->getParameter($keyword);

        return $this->user->whereRaw("(name LIKE ? OR email LIKE ?)", $parameter)
            ->get();
    }

    public function allSearchPage(string $keyword, int $perPage, int $page): Collection
    {
        $parameter = $this->getParameter($keyword);

        return $this->user->whereRaw("(name LIKE ? OR email LIKE ?)", $parameter)
            ->simplePaginate(perPage: $perPage, page: $page);
    }

    public function register(RegisterDataRequest $request): BaseEntity
    {
        $user = new $this->user;

        $user->full_name = $request->getFullName();
        $user->nick_name = $request->getNickName();
        $user->email = $request->getEmail();

        $this->setAuditableInformationFromRequest($user, $request);

        $user->setAttribute('password', bcrypt($request->password));

        $user->save();

        return $user->fresh();
    }

    public function revokeToken(string $email): BaseEntity|null
    {
        $user = $this->user->where("email", $email)
            ->get();

        if ($user->count() < 1) {
            return null;
        }

        $user->last()->oAuth()->delete();

        return $user->last();
    }

    private function getParameter(string $keyword) {
        return [
            'name' => '%' . $keyword . '%',
            'email' => '%' . $keyword . '%'
        ];
    }
}
