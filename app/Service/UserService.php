<?php

namespace App\Service;

use App\Core\Application\Request\SearchPageRequest;
use App\Core\Application\Request\SearchRequest;
use App\Repository\Contract\IUserRepository;
use App\Service\Contract\IUserService;

class UserService extends BaseService implements IUserService
{
    public IUserRepository $userRepository;

    /**
     * @param IUserRepository $userRepository
     */
    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAll()
    {
        // TODO: Implement getAll() method.
    }

    public function getAllSearch(SearchRequest $searchRequest)
    {
        // TODO: Implement getAllSearch() method.
    }

    public function getAllSearchPage(SearchPageRequest $searchPageRequest)
    {
        // TODO: Implement getAllSearchPage() method.
    }
}
