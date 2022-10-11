<?php

namespace App\Core\Application\Response;

use Illuminate\Support\Collection;

class BasicResponse
{
    public string $type;

    public int $codeStatus;

    private Collection $_messages;

    public Collection $messages;


    public function __construct()
    {
        $this->messages = new Collection();
    }


    public function getType(): string
    {
        return $this->type;
    }

    public function getCodeStatus(): int
    {
        return $this->codeStatus;
    }

    public function getMessages(): Collection
    {
        return $this->messages ?? $this->_messages = new Collection([MessageResponse::class]);
    }


    public function isError(): bool
    {
        return $this->messages->filter(function($item) {
                return $item->messageType === MessageType::ERROR;
            })->count() > 0;
    }

    public function isInfo(): bool
    {
        return $this->messages->filter(function($item) {
                return $item->messageType === MessageType::INFO;
            })->count() > 0;
    }

    public function isWarning(): bool
    {
        return $this->messages->filter(function($item) {
                return $item->messageType === MessageType::WARNING;
            })->count() > 0;
    }

    public function isSuccess(): bool
    {
        return $this->messages->filter(function($item) {
                return $item->messageType === MessageType::SUCCESS;
            })->count() > 0;
    }


    public function addErrorMessageResponse(string $error): void
    {
        $this->messages->push(
            new MessageResponse(MessageType::ERROR,
                $error)
        );
    }

    public function addInfoMessageResponse(string $info): void
    {
        $this->messages->push(
            new MessageResponse(MessageType::INFO,
                $info)
        );
    }

    public function addWarningMessageResponse(string $warning): void
    {
        $this->messages->push(
            new MessageResponse(MessageType::WARNING,
                $warning)
        );
    }

    public function addSuccessMessageResponse(string $success): void
    {
        $this->messages->push(
            new MessageResponse(MessageType::SUCCESS,
                $success)
        );
    }


    public function getMessageResponseAll(): array
    {
        $response = new Collection();

        $this->messages->each(function($item, $key) use($response) {
            $response->push($item->text);
        })->all();

        return $response->toArray();
    }

    public function getMessageResponseAllLatest(): string
    {
        $response = new Collection();

        $this->messages->each(function($item, $key) use($response) {
            $response->push($item->text);
        })->all();

        return $response->last();
    }

    public function getMessageResponseError(): array
    {
        $response = new Collection();

        $this->messages->each(function($item, $key) use($response) {
            if($item->messageType === MessageType::ERROR)
                $response->push($item->text);
        })->all();

        return $response->toArray();
    }

    public function getMessageResponseErrorLatest(): string
    {
        $response = new Collection();

        $this->messages->each(function($item, $key) use($response) {
            if($item->messageType === MessageType::ERROR)
                $response->push($item->text);
        })->all();

        return $response->last();
    }

    public function getMessageResponseInfo(): array
    {
        $response = new Collection();

        $this->messages->each(function($item, $key) use($response) {
            if($item->messageType === MessageType::INFO)
                $response->push($item->text);
        })->all();

        return $response->toArray();
    }

    public function getMessageResponseInfoLatest(): string
    {
        $response = new Collection();

        $this->messages->each(function($item, $key) use($response) {
            if($item->messageType === MessageType::INFO)
                $response->push($item->text);
        })->all();

        return $response->last();
    }

    public function getMessageResponseWarning(): array
    {
        $response = new Collection();

        $this->messages->each(function($item, $key) use($response) {
            if($item->messageType === MessageType::WARNING)
                $response->push($item->text);
        })->all();

        return $response->toArray();
    }

    public function getMessageResponseWarningLatest(): string
    {
        $response = new Collection();

        $this->messages->each(function($item, $key) use($response) {
            if($item->messageType === MessageType::WARNING)
                $response->push($item->text);
        })->all();

        return $response->last();
    }

    public function getMessageResponseSuccess(): array
    {
        $response = new Collection();

        $this->messages->each(function($item, $key) use($response) {
            if($item->messageType === MessageType::SUCCESS)
                $response->push($item->text);
        })->all();

        return $response->toArray();
    }

    public function getMessageResponseSuccessLatest(): string
    {
        $response = new Collection();

        $this->messages->each(function($item, $key) use($response) {
            if($item->messageType === MessageType::SUCCESS)
                $response->push($item->text);
        })->all();

        return $response->last();
    }

}
