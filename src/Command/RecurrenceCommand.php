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
    description: 'Gère les dépenses récurrentes automatiquement'
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
        $results = $this->depenseRepo->findDepensesRecurrentesDuJour();

        foreach ($results as $index => $row) {
            $output->writeln("Dépense #$index : " . json_encode($row));
            $nouvelleDepense = new Depense();
            $nouvelleDepense->setMontantDepense($row['montant_depense']);
            $nouvelleDepense->setDescriptionDepense($row['description_depense']);
            $nouvelleDepense->setDateDepense(new \DateTime()); 
            $nouvelleDepense->setCategorie($this->em->getReference('App\Entity\Categorie', $row['categorie_id']));
            $nouvelleDepense->setLivret($this->em->getReference('App\Entity\Livret', $row['livret_id']));

            $this->em->persist($nouvelleDepense);
            $this->em->flush(); 
            $recurrence = $this->em->getRepository(Recurrence::class)->find($row['recurrence_id']);
            if ($recurrence) {
                $recurrence->setDepense($nouvelleDepense);
                $this->em->persist($recurrence);
            }
        }

        $this->em->flush();

        $output->writeln("Dépenses récurrentes générées et mises à jour avec succès.");
        return Command::SUCCESS;
    }
}
