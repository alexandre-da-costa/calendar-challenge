<?php

namespace App\Traits\UserGems;

trait CallsUserGemsApi
{
    public function prepareRequest(): void
    {
        $this->httpClient
            ->baseUrl(config('usergems.api_base_url'))
            ->acceptJson();
    }
}
