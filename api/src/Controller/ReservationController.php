<?php

namespace App\Controller;

use App\Service\ReservationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Seance;
use App\Entity\User;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\ReservationException;


// le contrôleur ReservationController est responsable de gérer les requêtes liées aux réservations, en utilisant le service ReservationService pour effectuer les opérations nécessaires
final class ReservationController extends AbstractController
{
    public function __construct(
        private ReservationService $reservationService,
        private EntityManagerInterface $entityManager
    ) {
    }
    // On vérifie d'abord que Symfony reçoit correctement l'ID.
    #[Route('/api/seances/{id}/reservations', name: 'api_reservation_create', methods: ['POST'])]
    public function create(int $id): JsonResponse
    {
        // On récupère la séance correspondante à l'ID fourni dans la requête.
        $seance = $this->entityManager->getRepository(Seance::class)->find($id);

        if (!$seance) {
            return $this->json([
                'message' => 'Séance introuvable.'
            ], 404);
        }

        // On récupère l'utilisateur actuellement authentifié.
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json([
                'message' => 'Utilisateur non authentifié.'
            ], 401);
        }

        // On tente de créer une réservation pour l'utilisateur et la séance spécifiés, en gérant les exceptions éventuelles.
        try {
            // On appelle la méthode reserve du service ReservationService pour créer la réservation.
            $reservation = $this->reservationService->reserve($user, $seance);
            // On persiste la réservation dans la base de données.
        } catch (ReservationException $e) {
            return $this->json([
                'message' => $e->getMessage(),
            ], 409);
        }
        return $this->json([
            'message' => 'Réservation créée.',
            'reservationId' => $reservation->getId(),
        ], 201);
    }

    // On vérifie d'abord que Symfony reçoit correctement l'ID.
    #[Route('/api/reservations/{id}', name: 'api_reservation_delete', methods: ['DELETE'])]
        public function delete(int $id): JsonResponse
        {
            // On récupère la réservation correspondante à l'ID fourni dans la requête.
            $reservation = $this->entityManager
                ->getRepository(Reservation::class)
                ->find($id);

            // Si la réservation n'existe pas, on retourne une réponse JSON avec un message d'erreur et un code HTTP 404 (Not Found).
            if (!$reservation) {
                return $this->json([
                    'message' => 'Réservation introuvable.'
                ], 404);
            }

            $user = $this->getUser();

            // On vérifie si l'utilisateur est authentifié et s'il est le propriétaire de la réservation. Si ce n'est pas le cas, on retourne une réponse JSON avec un message d'erreur et un code HTTP approprié (401 pour non authentifié, 403 pour accès interdit).
            if (!$user instanceof User) {
                return $this->json([
                    'message' => 'Utilisateur non authentifié.'
                ], 401);
            }
            // On vérifie si l'utilisateur est le propriétaire de la réservation. Si ce n'est pas le cas, on retourne une réponse JSON avec un message d'erreur et un code HTTP 403 (Forbidden).
            if ($reservation->getUser()?->getId() !== $user->getId()) {
                return $this->json([
                    'message' => 'Vous ne pouvez pas annuler cette réservation.'
                ], 403);
            }

            // On tente d'annuler la réservation en appelant la méthode cancel du service ReservationService, en gérant les exceptions éventuelles.
            try {
                // On appelle la méthode cancel du service ReservationService pour annuler la réservation.
                $this->reservationService->cancel($user, $reservation);
                // On supprime la réservation de la base de données.
            } catch (ReservationException $e) {
                // Si une exception est levée, on retourne une réponse JSON avec le message d'erreur et un code HTTP 409 (Conflict).
                return $this->json([
                    'message' => $e->getMessage(),
                ], 409);
            }
            // Si l'annulation est réussie, on retourne une réponse JSON avec un message de succès et un code HTTP 200 (OK).
            return $this->json([
                'message' => 'Réservation annulée.',
            ], 200);
        }
    // Cette méthode retourne les réservations de l'utilisateur actuellement authentifié. Elle est accessible via la route '/api/me/reservations' avec la méthode HTTP GET.
    #[Route('/api/me/reservations', name: 'api_my_reservations', methods: ['GET'])]
        public function myReservations(): JsonResponse
        {
            //récupérer le membre connecté
            $user = $this->getUser();

            if (!$user instanceof User) {
                return $this->json([
                    'message' => 'Utilisateur non authentifié.'
                ], 401);
            }

            //demander à Doctrine de chercher les réservations appartenant à $user
            $reservations = $this->entityManager
                ->getRepository(Reservation::class)
                ->findBy(['user' => $user]);

            //préparer la liste des réservations
            $data = [];

            foreach ($reservations as $reservation) {
                //transforme chaque objet Reservation en un petit tableau JSON avec seulement les informations utiles
                //transformation/sérialisation vers les données de sortie
                $data[] = [
                    'id' => $reservation->getId(),
                    'seanceId' => $reservation->getSeance()->getId(),
                    'titre' => $reservation->getSeance()->getTitre(),
                    'dateHeure' => $reservation->getSeance()->getDateHeure()->format('Y-m-d H:i:s'),
                    'dateReservation' => $reservation->getDateReservation()->format('Y-m-d H:i:s'),
                ];
            }

            return $this->json([
                'reservations' => $data,
            ]);
        }
}