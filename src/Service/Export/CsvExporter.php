<?php
namespace App\Service\Export;
use App\Entity\Company;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\StreamedResponse;
class CsvExporter
{
    public function getResponseFromQueryBuilder(QueryBuilder $queryBuilder, $columns, $filename)
    {
        $entities = new ArrayCollection($queryBuilder->getQuery()->getResult());
        $response = new StreamedResponse();
        if (is_string($columns)) {
            $columns = $this->getColumnsForEntity($columns);
        }
        $response->setCallback(function () use ($entities, $columns) {
            $handle = fopen('php://output', 'w+');
            // Add header
            fputcsv($handle, array_keys($columns));
            while ($entity = $entities->current()) {
                $values = [];
                foreach ($columns as $column => $callback) {
                    $value = $callback;
                    if (is_callable($callback)) {
                        $value = $callback($entity);
                    }
                    $values[] = $value;
                }
                fputcsv($handle, $values);
                $entities->next();
            }
            fclose($handle);
        });
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        return $response;
    }

    private function getColumnsForEntity($class)
    {
        $columns[Company::class] = [
            'name' => function (Company $company) {
                return $company->getName();
            },
            'Email' => function (Company $company) {
                return $company->getEmail();
            },
            'Téléphone' => function (Company $company) {
                return $company->getPhone();
            },
            'Prénom/Nom de l\'admin entreprise' => function (Company $company) {
                return $company->getCompanyAdministratorFullName();
            },
            'Email l\'admin entreprise' => function (Company $company) {
                return $company->getEmailAdministrator();
            },
             'Fiche entreprise renseignée' => function (Company $company) {
                return $company->getIsCompleted();
            },
            'Fiche entreprise renseignée' => function (Company $company) {
                return $company->getIsCompleted();
            },
            'Logo' => function (Company $company) {
                return $company->isLogoDefined();
            },
            'Profil renseigné' => function (Company $company) {
                return $company->isProfileAdminCompleted();
            },
            'Photo' => function (Company $company) {
                return $company->isLogoAdminCompleted();
            },

            'Nombre de service' => function (Company $company) {
                return $company->getServiceNumber();
            },

            'Nombre de personnes parrainées' => function (Company $company) {
                return $company->getSponsorshipNumber();
            },
            'Date de creation' => function (Company $company) {
                return $company->getCreatedAt()->format('d/m/Y');
            },
            'Nombre de points' => function (Company $company) {
                return $company->getScore();
            },
            'Statut' => function (Company $company) {
                return ($company->isValid()) ? 'Active' : 'Inactive';
            },

        ];
        if (array_key_exists($class, $columns)) {
            return $columns[$class];
        }
        throw new \InvalidArgumentException(sprintf(
            'No columns set for "%s" entity',
            $class
        ));
    }

}