<?php

namespace App\Services;

class TranslationService
{
    private array $paysLangue = [
        // Afrique francophone
        'togo'        => 'fr', 'benin'       => 'fr', 'senegal'   => 'fr',
        'cote d\'ivoire' => 'fr', "côte d'ivoire" => 'fr',
        'cameroun'    => 'fr', 'mali'         => 'fr', 'guinee'    => 'fr',
        'burkina faso' => 'fr', 'niger'       => 'fr', 'congo'     => 'fr',
        'gabon'       => 'fr', 'rdc'          => 'fr',
        // Maghreb
        'maroc'       => 'ar', 'algerie'      => 'ar', 'tunisie'   => 'ar',
        // Afrique anglophone
        'nigeria'     => 'en', 'ghana'        => 'en', 'kenya'     => 'en',
        'south africa' => 'en', 'ouganda'     => 'en', 'tanzanie'  => 'sw',
        // Lusophone
        'mozambique'  => 'pt', 'angola'       => 'pt', 'cap-vert'  => 'pt',
        // Reste du monde
        'france'      => 'fr', 'belgique'     => 'fr',
        'usa'         => 'en', 'united states' => 'en',
        'uk'          => 'en', 'united kingdom' => 'en',
    ];

    public function detecterLangue(string $pays, string $langueUser = 'fr'): string
    {
        $pays  = strtolower(trim($pays));
        foreach ($this->paysLangue as $motCle => $langue) {
            if (str_contains($pays, $motCle)) {
                return $langue;
            }
        }
        return $langueUser;
    }
}
