<?php

namespace App\Repository;

use App\Application\Request\CreateLabelDataRequest;
use App\Application\Request\UpdateIpAddressDataRequest;
use App\Core\Domain\BaseEntity;
use App\Domain\Label;
use App\Repository\Contract\ILabelRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class LabelRepository extends BaseRepository implements ILabelRepository
{
    public Label $label;

    public function __construct(Label $label)
    {
        parent::__construct($label);
        $this->label = $label;
    }

    public function allSearch(string $keyword, string $order = "id", string $sort = "asc", array $args = []): Collection
    {
        $parameter = $this->getParameter($keyword);

        return $this->label->whereRaw("(title LIKE ?)", $parameter)
            ->orderBy($order, $sort)
            ->get();
    }

    public function allSearchPage(string $keyword, int $perPage, int $page, string $order = "id", string $sort = "asc", array $args = []): Paginator
    {
        $parameter = $this->getParameter($keyword);

        return $this->label->whereRaw("(title LIKE ?)", $parameter)
            ->orderBy($order, $sort)
            ->simplePaginate(perPage: $perPage, page: $page);
    }

    public function findByTitle(string $title): BaseEntity|null
    {
        $label = $this->label->where("title", $title)
            ->get();

        if ($label->count() < 1) {
            return null;
        }

        return $label->last();
    }

    public function save(CreateLabelDataRequest $request): BaseEntity
    {
        $label = new $this->label([
            "title" => $request->getTitle()
        ]);

        $this->setAuditableInformationFromRequest($label, $request);

        $label->save();

        return $label->fresh();
    }

    public function update(UpdateIpAddressDataRequest $request): BaseEntity|null
    {
        $label = $this->label->find($request->getId());

        if (!$label) {
            return null;
        }

        $this->setAuditableInformationFromRequest($label, $request);

        $label->save();

        return $label->fresh();
    }

    public function delete(int $id): int
    {
        $label = $this->label->find($id);

        return $label->delete();
    }

    private function getParameter(string $keyword) {
        return [
            'title' => '%' . $keyword . '%'
        ];
    }
}
