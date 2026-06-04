<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class AssociationSearchController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route('/%beacon_admin.route_prefix%/association-search', name: 'beacon_admin_association_search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $entityClass = $request->query->getString('entity', '');
        $query = $request->query->getString('q', '');
        $field = $request->query->getString('field', '');

        if ($entityClass === '' || !class_exists($entityClass)) {
            return $this->json(['results' => []]);
        }

        if ($field !== '' && !preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $field)) {
            return $this->json(['results' => []]);
        }

        $metadata = $this->em->getClassMetadata($entityClass);
        if ($field !== '' && !in_array($field, $metadata->getFieldNames(), true)) {
            return $this->json(['results' => []]);
        }

        $repository = $this->em->getRepository($entityClass);
        $qb = $repository->createQueryBuilder('e');

        if ($field !== '' && $query !== '') {
            $qb->andWhere("e.{$field} LIKE :q")
                ->setParameter('q', "%{$query}%");
        }

        $results = $qb->setMaxResults(20)->getQuery()->getResult();

        $items = [];
        foreach ($results as $entity) {
            $items[] = [
                'id' => $this->getEntityId($entity),
                'label' => (string) $entity,
                'value' => $this->getEntityId($entity),
            ];
        }

        return $this->json(['results' => $items]);
    }

    private function getEntityId(object $entity): string
    {
        $meta = $this->em->getClassMetadata($entity::class);
        $ids = $meta->getIdentifierValues($entity);

        return implode('-', $ids);
    }
}
