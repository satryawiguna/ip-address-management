<?php

namespace App\Infrastructure\Job;

use App\Application\Request\CreateIpAddressDataRequest;
use App\Repository\Contract\IIpAddressRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateIpAddress implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public IIpAddressRepository $ipAddressRepository;

    public CreateIpAddressDataRequest $request;

    public function __construct(IIpAddressRepository $ipAddressRepository, CreateIpAddressDataRequest $request)
    {
        $this->ipAddressRepository = $ipAddressRepository;
        $this->request = $request;
    }

    public function handle()
    {
        return $this->ipAddressRepository->save($this->request);
    }
}
