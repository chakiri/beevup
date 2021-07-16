<?php

namespace App\Command;

use App\Entity\Commune;
use App\Service\Map\GeocodeAddress;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiExternCommunesCommand extends Command
{
    protected static $defaultName = 'app:api-extern-communes';
    private HttpClientInterface $client;
    private EntityManagerInterface $manager;
    private GeocodeAddress $geocodeAddress;

    /**
     * ApiExternCommunesCommand constructor.
     */
    public function __construct(HttpClientInterface $client, EntityManagerInterface $manager, GeocodeAddress $geocodeAddress)
    {
        parent::__construct(null);
        $this->client = $client;
        $this->manager = $manager;
        $this->geocodeAddress = $geocodeAddress;
    }

    protected function configure()
    {
        $this
            ->setDescription('Command to get data communes from extern api')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->client->request(
            'GET',
            'https://geo.api.gouv.fr/communes'
        );

        foreach ($response->toArray() as $value){
            $commune = new Commune();

            $commune->setName($value['nom']);

            //Check if commune has more than one postal code
            if (count($value['codesPostaux']) > 1){
                foreach ($value['codesPostaux'] as $postalCode){
                    $commune = new Commune();

                    $commune->setName($value['nom'])
                        ->setPostalCode($postalCode)
                    ;

                    if (array_key_exists('codeDepartement', $value)){
                        $commune->setDepartment($value['codeDepartement']);
                    }

                    //Calcul lat and long from postal code
                    $locate = $this->geocodeAddress->geocodePostalCode($postalCode . '-' . $value['nom']);

                    if ($locate){
                        dump($locate[0], $locate[1]);
                        $commune->setLatitude($locate[0])
                            ->setLongitude($locate[1]);
                    }

                    $this->manager->persist($commune);
                }
                $this->manager->flush();
            }else{
                $commune = new Commune();

                $commune->setName($value['nom'])
                    ->setPostalCode($value['codesPostaux'][0])
                ;

                if (array_key_exists('codeDepartement', $value)){
                    $commune->setDepartment($value['codeDepartement']);
                }

                //Calcul lat and long from postal code
                $locate = $this->geocodeAddress->geocodePostalCode($value['codesPostaux'][0] . '-' . $value['nom']);

                if ($locate){
                    dump($locate[0], $locate[1]);
                    $commune->setLatitude($locate[0])
                        ->setLongitude($locate[1]);
                }

                $this->manager->persist($commune);
                $this->manager->flush();
            }
        }

        return 1;
    }
}
