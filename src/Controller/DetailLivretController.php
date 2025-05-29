<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Livret;
use App\Entity\Avoir;
use App\Entity\Categorie;
use App\Entity\Depense;
use DateTime;
use DateInterval;

final class DetailLivretController extends AbstractController
{
    #[Route('/lesLivrets/{id}/detail', name: 'detailLivret')]
    public function detail(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $livret = $em->getRepository(Livret::class)->find($id);
        if (!$livret) {
            throw $this->createNotFoundException('Livret non trouvé');
        }

        $mois = $request->query->getInt('mois', (int) date('m'));
        $annee = $request->query->getInt('annee', (int) date('Y'));

        $dateDebut = new DateTime("$annee-$mois-01");
        $dateFin = (clone $dateDebut)->add(new DateInterval('P1M'));

        $moisPrecedent = $mois == 1 ? 12 : $mois - 1;
        $anneePrecedente = $mois == 1 ? $annee - 1 : $annee;

        $moisSuivant = $mois == 12 ? 1 : $mois + 1;
        $anneeSuivante = $mois == 12 ? $annee + 1 : $annee;

        $avoirs = $livret->getAvoirs();
        $avoirsFiltrés = [];

        foreach ($avoirs as $avoir) {
            $categorie = $avoir->getCategorie();

            $aDesDepensesDansLeMois = false;
            foreach ($categorie->getDepenses() as $depense) {
                if (
                    $depense->getLivret()->getId() === $livret->getId() &&
                    $depense->getDate() >= $dateDebut &&
                    $depense->getDate() < $dateFin
                ) {
                    $aDesDepensesDansLeMois = true;
                    break;
                }
            }

            if ($avoir->estActif() || $aDesDepensesDansLeMois) {
                if (!in_array($avoir, $avoirsFiltrés, true)) {
                    $avoirsFiltrés[] = $avoir;
                }
            }
        }

        $categories = $em->getRepository(Categorie::class)->categOrdreAsc();
        $categoriesDansLivret = $em->getRepository(Categorie::class)->categDansLivret($livret->getId());

        return $this->render('detailLivret.html.twig', [
            'livret' => $livret,
            'categories' => $categories,
            'categoriesDansLivret' => $categoriesDansLivret,
            'avoirs' => $avoirsFiltrés,
            'mois' => $mois,
            'annee' => $annee,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin,
            'moisPrecedent' => $moisPrecedent,
            'anneePrecedente' => $anneePrecedente,
            'moisSuivant' => $moisSuivant,
            'anneeSuivante' => $anneeSuivante,
        ]);
    }
}