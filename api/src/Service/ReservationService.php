<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\Seance;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\ReservationException;

class ReservationService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }
    // compte le nombre de réservations pour une séance donnée
    public function countReservations(Seance $seance): int
    {
        return count($seance->getReservations());
    }
    // vérifie si une séance est complète en comparant le nombre de réservations avec le nombre total de places disponibles
    public function isFull(Seance $seance): bool
    {
        return $this->countReservations($seance) >= $seance->getPlacesTotal();
    }
    // vérifie si un utilisateur a déjà réservé une séance donnée
    public function isPast(Seance $seance): bool
    {
        return $seance->getDateHeure() < new \DateTime();
    }
    // vérifie si un utilisateur a déjà réservé une séance donnée
    public function hasAlreadyReserved(User $user, Seance $seance): bool
    {
        foreach ($seance->getReservations() as $reservation) {
            if ($reservation->getUser()?->getId() === $user->getId()) {
                return true;
            }
        }

        return false;
    }
    // crée une nouvelle réservation pour un utilisateur et une séance donnée, en vérifiant d'abord si la séance est passée, complète ou déjà réservée par l'utilisateur
    public function reserve(User $user, Seance $seance): Reservation
    {
        if ($this->isPast($seance)) {
            throw new ReservationException('Impossible de réserver une séance passée.');
        }

        if ($this->isFull($seance)) {
            throw new  ReservationException('La séance est complète.');
        }

        if ($this->hasAlreadyReserved($user, $seance)) {
            throw new ReservationException('Vous avez déjà réservé cette séance.');
        }

        $reservation = new Reservation();
        $reservation->setUser($user);
        $reservation->setSeance($seance);
        $reservation->setDateReservation(new \DateTime());

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        return $reservation;
    }
    public function cancel(User $user, Reservation $reservation): void
    {
        if ($reservation->getUser() !== $user) {
            throw new ReservationException(
                'Vous ne pouvez pas annuler cette réservation.'
            );
        }

        if ($reservation->getSeance()->getDateHeure() < new \DateTime()) {
            throw new ReservationException(
                'Impossible d’annuler une réservation pour une séance passée.'
            );
        }

        $this->entityManager->remove($reservation);
        $this->entityManager->flush();
    }
}
