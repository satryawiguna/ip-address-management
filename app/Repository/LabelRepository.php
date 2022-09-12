<?php

namespace App\Repository;

use App\Application\Request\CreateLabeldataRequest;
use App\Core\Domain\BaseEntity;
use App\Domain\Label;
use App\Repository\Contract\ILabelRepository;

class LabelRepository extends BaseRepository implements ILabelRepository
{
    public Label $label;

    public function __construct(Label $label)
    {
        parent::__construct($label);
        $this->label = $label;
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

    public function save(CreateLabeldataRequest $request): BaseEntity
    {
        $label = new $this->label([
            "title" => $request->getTitle()
        ]);

        $this->setAuditableInformationFromRequest($label, $request);

        $label->save();

        return $label->fresh();
    }

}
