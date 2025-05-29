<?php

namespace App\Command;

use App\Repository\DepenseRepository;
use App\Entity\Depense;
use App\Entity\Recurrence;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:depense-recurrente',
    description: 'Gère les dépenses récurrentes chaque 1er du mois'
)]
class RecurrenceCommand extends Command
{
    private EntityManagerInterface $em;
    private DepenseRepository $depenseRepo;


    public function __construct(EntityManagerInterface $em, DepenseRepository $depenseRepo)
    {
        parent::__construct();
        $this->em = $em;
        $this->depenseRepo = $depenseRepo;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        date_default_timezone_set('Europe/Paris');
        $maintenant = new \DateTime();
        $moisActuel = (int) $maintenant->format('m');
        $anneeActuelle = (int) $maintenant->format('Y');

        $moisPrecedent = $moisActuel === 1 ? 12 : $moisActuel - 1;
        $anneePrecedente = $moisActuel === 1 ? $anneeActuelle - 1 : $anneeActuelle;

        $dateDebut = new \DateTime("$anneePrecedente-$moisPrecedent-01");
        $dateFin = (clone $dateDebut)->modify('+1 month');

        $output->writeln("Traitement des dépenses du $moisPrecedent/$anneePrecedente...");

        $depenses = $this->depenseRepo->depensesDuMois($dateDebut, $dateFin);

        foreach ($depenses as $depense) {
            if ($depense->estRecurrente()) {
                $nouvelleDate = new \DateTime("$anneeActuelle-$moisActuel-" . $depense->getDate()->format('d'));

                if (!checkdate((int) $nouvelleDate->format('m'), (int) $nouvelleDate->format('d'), (int) $nouvelleDate->format('Y'))) {
                    $nouvelleDate->modify('last day of this month');
                }

                $nouvelleDepense = new Depense();
                $nouvelleDepense->setMontantDepense($depense->getMontant());
                $nouvelleDepense->setDescriptionDepense($depense->getDescription());
                $nouvelleDepense->setDateDepense($nouvelleDate);
                $nouvelleDepense->setLivret($depense->getLivret());
                $nouvelleDepense->setCategorie($depense->getCategorie());
                $nouvelleDepense->setEstRecurrente(true);

                $this->em->persist($nouvelleDepense);

                $output->writeln("Dépense récurrente faites pour le " . $nouvelleDate->format('d/m/Y'));
            }
        }

        $this->em->flush();

        $output->writeln("Traitement terminé pour le " . $maintenant->format('d/m/Y'));

        return Command::SUCCESS;
    }
}