<?php

namespace App\Services\UserGems;

use App\Interfaces\PersonEnrichmentInterface;
use App\Models\ClientCompany;
use App\Models\Person;
use App\Traits\UserGems\CallsUserGemsApi;
use Illuminate\Http\Client\PendingRequest;

class PersonDataService implements PersonEnrichmentInterface
{
    use CallsUserGemsApi;

    public function __construct(
        private readonly PendingRequest $httpClient,
        private readonly Person $person,
        private readonly ClientCompany $clientCompany,
    ) {
        $this->prepareRequest();
    }

    public function enrichPersonDataByEmail(string $email): Person
    {
        $personData = $this->fetchPersonDataByEmail($email);

        return $this->person->updateOrCreate(
            compact('email'),
            [
                'first_name' => $personData['first_name'],
                'last_name' => $personData['last_name'],
                'title' => $personData['title'],
                'avatar_url' => $personData['avatar'],
                'linkedin_profile_url' => $personData['linkedin_url'],
                'client_company_id' => $this->clientCompany->firstOrCreate([
                    'name' => $personData['company']['name'],
                    'linkedin_page_url' => $personData['company']['linkedin_url'],
                    'employees_count' => $personData['company']['employees'],
                ])->id,
            ]
        );
    }

    private function fetchPersonDataByEmail(string $email): mixed
    {
        $this->httpClient->withToken(config('usergems.person_data.api_key'));
        $response = $this->httpClient->get(config('usergems.person_data.endpoint').$email);

        return $response->json();
    }
}
