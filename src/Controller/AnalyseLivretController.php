<?php

namespace App\Controller;

use App\Entity\Livret;
use App\Entity\Depense;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AnalyseLivretController extends AbstractController
{
    #[Route('/lesLivrets/{id}/detail/analyse', name: 'analyse')]
    public function analyse(int $id, EntityManagerInterface $em): Response
    {
        $livret = $em->getRepository(Livret::class)->find($id);

        if (!$livret) {
            $this->addFlash('danger', 'Données invalides.');
            return $this->redirectToRoute('detailLivret', ['id' => $id]);
        }

        $donnees = $em->getRepository(Depense::class)->getSommeParCategorieParLivret($id);

        $labels = [];
        $montants = [];

        foreach ($donnees as $row) {
            $labels[] = $row['categorie'];
            $montants[] = $row['total'];
        }

        return $this->render('analyseLivret.html.twig', [
            'livret' => $livret,
            'labels' => json_encode($labels),
            'montants' => json_encode($montants),
        ]);
    }

}
