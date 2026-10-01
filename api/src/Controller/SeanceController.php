<?php

namespace App\Controller;

use App\Entity\Seance;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class SeanceController extends AbstractController
{
    
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }
    #[Route('/api/seances', name: 'api_seances_list', methods: ['GET'])]
    // récupère toutes les séances à venir
    public function index(): JsonResponse
    {
        $seances = $this->entityManager
            ->getRepository(Seance::class)
            ->findAll();

        $seancesAVenir = [];

        foreach ($seances as $seance) {
            if ($seance->getDateHeure() > new \DateTime()) {
                $seancesAVenir[] = [
                    'id' => $seance->getId(),
                    'titre' => $seance->getTitre(),
                    'dateHeure' => $seance->getDateHeure()->format('Y-m-d H:i:s'),
                    'duree' => $seance->getDuree(),
                    'placesTotal' => $seance->getPlacesTotal(),
                    'placesRestantes' => $seance->getPlacesTotal() - count($seance->getReservations()),
                ];
            }
        }

        return $this->json([
            'nombre' => count($seancesAVenir),
            'seances' => $seancesAVenir,
        ]);
    }
    #[Route('/api/seances/{id}', name: 'api_seance_detail', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $seance = $this->entityManager
            ->getRepository(Seance::class)
            ->find($id);

        if (!$seance) {
            return $this->json([
                'message' => 'Séance introuvable.'
            ], 404);
        }

        return $this->json([
            'id' => $seance->getId(),
            'titre' => $seance->getTitre(),
            'description' => $seance->getDescription(),
            'dateHeure' => $seance->getDateHeure()->format('Y-m-d H:i:s'),
            'duree' => $seance->getDuree(),
            'placesTotal' => $seance->getPlacesTotal(),
            'placesRestantes' => $seance->getPlacesTotal() - count($seance->getReservations()),
        ]);
    }

    #[Route('/api/seances', name: 'api_seance_create', methods: ['POST'])]
    #[IsGranted('ROLE_COACH')]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $seance = new Seance();

        $seance->setTitre($data['titre']);
        $seance->setDescription($data['description']);
        $seance->setDateHeure(new \DateTime($data['dateHeure']));
        $seance->setDuree($data['duree']);
        $seance->setPlacesTotal($data['placesTotal']);

        $this->entityManager->persist($seance);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Séance créée.',
            'id' => $seance->getId(),
        ], 201);
    }

    #[Route('/api/seances/{id}', name: 'api_seance_update', methods: ['PUT'])]
    #[IsGranted('ROLE_COACH')]
    //modification d'une séance
    public function update(int $id, Request $request): JsonResponse
    {
        $seance = $this->entityManager
            ->getRepository(Seance::class)
            ->find($id);

        if (!$seance) {
            return $this->json([
                'message' => 'Séance introuvable.'
            ], 404);
        }

        $data = json_decode($request->getContent(), true);

        $seance->setTitre($data['titre']);
        $seance->setDescription($data['description']);
        $seance->setDateHeure(new \DateTime($data['dateHeure']));
        $seance->setDuree($data['duree']);
        $seance->setPlacesTotal($data['placesTotal']);

        $this->entityManager->flush();

        return $this->json([
            'message' => 'Séance modifiée.',
            'id' => $seance->getId(),
        ]);
    }
    #[Route('/api/seances/{id}', name: 'api_seance_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_COACH')]
    public function delete(int $id): JsonResponse
    {
        // On récupère la séance correspondante à l'ID fourni dans la requête.
        $seance = $this->entityManager
            ->getRepository(Seance::class)
            ->find($id);

        if (!$seance) {
            return $this->json([
                'message' => 'Séance introuvable.'
            ], 404);
        }

        $this->entityManager->remove($seance);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Séance supprimée.',
        ]);
    }
    #[Route('/api/seances/{id}/participants', name: 'api_seance_participants', methods: ['GET'])]
    #[IsGranted('ROLE_COACH')]
    
    public function participants(int $id): JsonResponse
    {
        // On récupère la séance correspondante à l'ID fourni dans la requête.
        $seance = $this->entityManager
            ->getRepository(Seance::class)
            ->find($id);

        if (!$seance) {
            return $this->json([
                'message' => 'Séance introuvable.'
            ], 404);
        }

        $participants = [];

        foreach ($seance->getReservations() as $reservation) {
            $user = $reservation->getUser();

            $participants[] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
            ];
        }

        return $this->json([
            'seanceId' => $seance->getId(),
            'titre' => $seance->getTitre(),
            'participants' => $participants,
        ]);
    }
    
}
