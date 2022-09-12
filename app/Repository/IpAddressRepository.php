<?php

namespace App\Repository;

use App\Application\Request\CreateIpAddressDataRequest;
use App\Application\Request\CreateLabeldataRequest;
use App\Application\Request\UpdateIpAddressDataRequest;
use App\Core\Domain\BaseEntity;
use App\Domain\IpAddress;
use App\Presentation\Http\Controllers\RequestAuthor;
use App\Repository\Contract\IIpAddressRepository;
use App\Repository\Contract\ILabelRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class IpAddressRepository extends BaseRepository implements IIpAddressRepository
{
    use RequestAuthor;

    public IpAddress $ipAddress;

    public ILabelRepository $labelRepository;

    public function __construct(IpAddress $ipAddress,
        ILabelRepository $labelRepository)
    {
        parent::__construct($ipAddress);
        $this->ipAddress = $ipAddress;
        $this->labelRepository = $labelRepository;
    }

    public function findById(int|string $id): BaseEntity|null
    {
        return $this->ipAddress->with(["labels"])
            ->find($id);
    }

    public function allSearch(string $keyword, string $order = "id", string $sort = "asc", array $args = []): Collection
    {
        $parameter = $this->getParameter($keyword);

        $result = $this->ipAddress->whereRaw("(ipv4 LIKE ?)", $parameter);

        if (array_key_exists("label", $args)) {
            $result = $result->whereHas("labels", function($query) use($args) {
                $query->where('title', $args["label"]);
            });
        }

        return $result->orderBy($order, $sort)
            ->get();
    }

    public function allSearchPage(string $keyword, int $perPage, int $page, string $order = "id", string $sort = "asc", array $args = []): Paginator
    {
        $parameter = $this->getParameter($keyword);

        $result = $this->ipAddress->whereRaw("(ipv4 LIKE ?)", $parameter);

        if (array_key_exists("label", $args)) {
            $result = $result->whereHas("labels", function($query) use($args) {
                $query->where('title', $args["label"]);
            });
        }

        return $result->orderBy($order, $sort)
            ->simplePaginate(perPage: $perPage, page: $page);
    }

    public function save(CreateIpAddressDataRequest $request): BaseEntity
    {
        $ipAddress = new $this->ipAddress([
            "ipv4" => $request->getIpv4()
        ]);

        $this->setAuditableInformationFromRequest($ipAddress, $request);

        $label = $this->labelRepository->findByTitle($request->label);

        if (!$label) {
            $createLabelDataRequest = new CreateLabeldataRequest();
            $createLabelDataRequest->setTitle($request->getLabel());

            $this->setRequestAuthor($createLabelDataRequest);

            $label = $this->labelRepository->save($createLabelDataRequest);
        }

        $ipAddress->save();

        $ipAddress->labels()->attach($label);

        return $ipAddress->fresh();
    }

    public function update(UpdateIpAddressDataRequest $request): BaseEntity|null
    {
        $ipAddress = $this->ipAddress->find($request->getId());

        if (!$ipAddress) {
            return null;
        }

        $ipAddress->setAttributes([
            "ipv4" => $request->getIpv4()
        ]);

        $this->setAuditableInformationFromRequest($ipAddress, $request);

        $label = $this->labelRepository->findByTitle($request->label);

        if (!$label) {
            $createLabelDataRequest = new CreateLabeldataRequest();
            $createLabelDataRequest->setTitle($request->getLabel());

            $label = $this->labelRepository->save($createLabelDataRequest);
        }

        $ipAddress->save();

        $ipAddress->labels->sync($label);

        return $ipAddress->fresh();
    }

    public function delete(int $id): int
    {
        // TODO: Implement delete() method.
    }

    private function getParameter(string $keyword) {
        return [
            'ipv4' => '%' . $keyword . '%'
        ];
    }

}
