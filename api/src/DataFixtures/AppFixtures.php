<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Seance;
use App\Entity\Reservation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Coach
        $coach = new User();
        $coach->setEmail('coach@opengym.test');
        $coach->setName('Coach OpenGym');
        $coach->setRoles(['ROLE_COACH']);
        $coach->setPassword(
            $this->passwordHasher->hashPassword($coach, 'Coach123!')
        );

        $manager->persist($coach);

        // Membre 1
        $member1 = new User();
        $member1->setEmail('membre1@opengym.test');
        $member1->setName('Membre 1');
        $member1->setRoles(['ROLE_MEMBER']);
        $member1->setPassword(
            $this->passwordHasher->hashPassword($member1, 'Membre123!')
        );

        $manager->persist($member1);

        // Membre 2
        $member2 = new User();
        $member2->setEmail('membre2@opengym.test');
        $member2->setName('Membre 2');
        $member2->setRoles(['ROLE_MEMBER']);
        $member2->setPassword(
            $this->passwordHasher->hashPassword($member2, 'Membre123!')
        );

        $manager->persist($member2);

        // Membre 3
        $member3 = new User();
        $member3->setEmail('membre3@opengym.test');
        $member3->setName('Membre 3');
        $member3->setRoles(['ROLE_MEMBER']);
        $member3->setPassword(
            $this->passwordHasher->hashPassword($member3, 'Membre123!')
        );

        $manager->persist($member3);

                // Séance 1 - à venir
        $seance1 = new Seance();
        $seance1->setTitre('Yoga débutant');
        $seance1->setDescription('Séance de yoga adaptée aux débutants.');
        $seance1->setDateHeure(new \DateTime('+2 days 18:00'));
        $seance1->setDuree(60);
        $seance1->setPlacesTotal(10);
        $manager->persist($seance1);

        // Séance 2 - à venir
        $seance2 = new Seance();
        $seance2->setTitre('Cardio Training');
        $seance2->setDescription('Séance cardio pour améliorer l’endurance.');
        $seance2->setDateHeure(new \DateTime('+3 days 19:00'));
        $seance2->setDuree(45);
        $seance2->setPlacesTotal(15);
        $manager->persist($seance2);

        // Séance 3 - à venir
        $seance3 = new Seance();
        $seance3->setTitre('Renforcement musculaire');
        $seance3->setDescription('Travail de renforcement musculaire général.');
        $seance3->setDateHeure(new \DateTime('+5 days 17:30'));
        $seance3->setDuree(60);
        $seance3->setPlacesTotal(12);
        $manager->persist($seance3);

        // Séance 4 - à venir
        $seance4 = new Seance();
        $seance4->setTitre('Pilates');
        $seance4->setDescription('Séance de Pilates et de mobilité.');
        $seance4->setDateHeure(new \DateTime('+7 days 18:30'));
        $seance4->setDuree(50);
        $seance4->setPlacesTotal(10);
        $manager->persist($seance4);

        // Séance 5 - à venir et destinée à être complète
        $seance5 = new Seance();
        $seance5->setTitre('Circuit Training');
        $seance5->setDescription('Circuit training avec un nombre limité de places.');
        $seance5->setDateHeure(new \DateTime('+4 days 18:00'));
        $seance5->setDuree(60);
        $seance5->setPlacesTotal(3);
        $manager->persist($seance5);

        // Séance 6 - passée
        $seance6 = new Seance();
        $seance6->setTitre('Stretching');
        $seance6->setDescription('Séance de stretching et récupération.');
        $seance6->setDateHeure(new \DateTime('-2 days 18:00'));
        $seance6->setDuree(45);
        $seance6->setPlacesTotal(10);
        $manager->persist($seance6);

                // Réservation 1 - séance complète
        $reservation1 = new Reservation();
        $reservation1->setUser($member1);
        $reservation1->setSeance($seance5);
        $reservation1->setDateReservation(new \DateTime());
        $manager->persist($reservation1);

                // Réservation 2 - séance complète
        $reservation2 = new Reservation();
        $reservation2->setUser($member2);
        $reservation2->setSeance($seance5);
        $reservation2->setDateReservation(new \DateTime());
        $manager->persist($reservation2);

                // Réservation 3 - séance complète
        $reservation3 = new Reservation();
        $reservation3->setUser($member3);
        $reservation3->setSeance($seance5);
        $reservation3->setDateReservation(new \DateTime());
        $manager->persist($reservation3);

        $manager->flush();
    }
}
